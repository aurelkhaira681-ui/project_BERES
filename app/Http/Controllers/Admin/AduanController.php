<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class AduanController extends Controller
{
    // Query dasar + filter (dipakai halaman daftar dan ekspor)
    private function filtered(Request $request)
    {
        $query = Aduan::with(['user', 'kategori', 'lokasi'])->latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_tiket', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('lokasi', fn ($l) => $l->where('nama_ruangan', 'like', "%{$search}%"));
            });
        }

        if ($kategori = $request->query('kategori')) {
            $query->where('kategori_kerusakan_id', $kategori);
        }
        if ($lokasi = $request->query('lokasi')) {
            $query->where('lokasi_id', $lokasi);
        }

        if ($status = $request->query('status')) {
            $status === 'disetujui'
                ? $query->whereIn('status', ['approved', 'selesai'])
                : $query->where('status', $status);
        }

        match ($request->query('tanggal')) {
            'hari-ini' => $query->whereDate('created_at', today()),
            '7-hari' => $query->where('created_at', '>=', now()->subDays(7)->startOfDay()),
            'bulan-ini' => $query->where('created_at', '>=', now()->startOfMonth()),
            default => null,
        };

        return $query;
    }

    // Halaman "Data Aduan" - daftar lengkap dengan pencarian & filter
    public function index(Request $request)
    {
        $aduan = $this->filtered($request)->paginate(15)->withQueryString();

        $stats = [
            'total' => Aduan::count(),
            'pending' => Aduan::where('status', 'pending')->count(),
            'disetujui' => Aduan::whereIn('status', ['approved', 'selesai'])->count(),
            'ditolak' => Aduan::where('status', 'rejected')->count(),
        ];

        $kategoriList = KategoriKerusakan::orderBy('nama')->get();
        $lokasiList = Lokasi::orderBy('nama_ruangan')->get();

        return view('admin.aduan.index', compact('aduan', 'stats', 'kategoriList', 'lokasiList'));
    }

    // Ekspor CSV sesuai filter yang sedang aktif
    public function export(Request $request)
    {
        $rows = $this->filtered($request)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar terbaca benar di Excel
            fputcsv($out, ['No. Tiket', 'Siswa', 'Kelas', 'Kategori', 'Lokasi', 'Tanggal', 'Status', 'Deskripsi']);
            foreach ($rows as $a) {
                fputcsv($out, [
                    $a->kode_tiket,
                    $a->user->name,
                    $a->user->kelas,
                    $a->kategori->nama,
                    $a->lokasi->nama_ruangan,
                    $a->tanggal_kejadian->format('d-m-Y'),
                    $a->statusLabel(),
                    $a->deskripsi,
                ]);
            }
            fclose($out);
        }, 'data-aduan-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Detail tiket untuk verifikasi (Setujui/Tolak/Selesai)
    public function show(Aduan $aduan)
    {
        $aduan->load(['user', 'kategori', 'lokasi']);
        $totalAduanPelapor = $aduan->user->aduan()->count();

        return view('admin.aduan.show', compact('aduan', 'totalAduanPelapor'));
    }

    public function setujui(Request $request, Aduan $aduan)
    {
        $aduan->update([
            'status' => 'approved',
            'catatan_admin' => $request->input('catatan_admin'),
            'diverifikasi_at' => now(),
        ]);

        return back()->with('success', "Aduan {$aduan->kode_tiket} disetujui.");
    }

    public function tolak(Request $request, Aduan $aduan)
    {
        $request->validate(['catatan_admin' => ['required', 'string']]);

        $aduan->update([
            'status' => 'rejected',
            'catatan_admin' => $request->input('catatan_admin'),
            'diverifikasi_at' => now(),
        ]);

        return back()->with('success', "Aduan {$aduan->kode_tiket} ditolak.");
    }

    public function selesai(Aduan $aduan)
    {
        abort_unless($aduan->status === 'approved', 403, 'Hanya aduan yang sudah disetujui yang dapat ditandai selesai.');

        $aduan->update([
            'status' => 'selesai',
            'selesai_at' => now(),
        ]);

        return back()->with('success', "Aduan {$aduan->kode_tiket} ditandai selesai.");
    }

    public function destroy(Aduan $aduan)
    {
        $aduan->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Tiket aduan dihapus.');
    }
}