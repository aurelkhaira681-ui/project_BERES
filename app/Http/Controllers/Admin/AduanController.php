<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\Teknisi;
use Illuminate\Http\Request;

class AduanController extends Controller
{
    // Query dasar + filter (dipakai halaman daftar dan ekspor)
    private function filtered(Request $request)
    {
        $query = Aduan::with(['user', 'kategori', 'lokasi', 'teknisi'])->latest();

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
        if ($teknisi = $request->query('teknisi')) {
            $teknisi === 'belum' ? $query->whereNull('teknisi_id') : $query->where('teknisi_id', $teknisi);
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
        $teknisiList = Teknisi::orderBy('nama')->get();

        return view('admin.aduan.index', compact('aduan', 'stats', 'kategoriList', 'lokasiList', 'teknisiList'));
    }

    // Ekspor CSV sesuai filter yang sedang aktif
    public function export(Request $request)
    {
        $rows = $this->filtered($request)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar terbaca benar di Excel
            fputcsv($out, ['No. Tiket', 'Siswa', 'Kelas', 'Kategori', 'Lokasi', 'Teknisi', 'Tanggal', 'Status', 'Deskripsi']);
            foreach ($rows as $a) {
                fputcsv($out, [
                    $a->kode_tiket,
                    $a->user->name,
                    $a->user->kelas,
                    $a->kategori->nama,
                    $a->lokasi->nama_ruangan,
                    $a->teknisi?->nama ?? '-',
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
        $aduan->load(['user', 'kategori', 'lokasi', 'teknisi']);
        $totalAduanPelapor = $aduan->user->aduan()->count();
        [$calonTeknisi, $fallbackTeknisi] = $this->calonTeknisi($aduan);

        return view('admin.aduan.show', compact('aduan', 'totalAduanPelapor', 'calonTeknisi', 'fallbackTeknisi'));
    }

    // Teknisi yang boleh ditugaskan: sejurusan dengan lokasi (lokasi umum = semua teknisi aktif).
    // Jika tidak ada teknisi aktif di jurusan itu (mis. semua cuti), tampilkan semua teknisi aktif.
    private function calonTeknisi(Aduan $aduan): array
    {
        $semua = Teknisi::where('aktif', true)->beban()->orderBy('aktif_count')->orderBy('nama')->get();
        $jurusan = $aduan->lokasi->jurusan;

        if (blank($jurusan)) {
            return [$semua, false];
        }

        $sejurusan = $semua->where('jurusan', $jurusan)->values();

        return $sejurusan->isNotEmpty() ? [$sejurusan, false] : [$semua, true];
    }

    public function tugaskan(Request $request, Aduan $aduan)
    {
        $request->validate(['teknisi_id' => ['required', 'integer']]);
        abort_unless(in_array($aduan->status, ['pending', 'approved']), 403);

        $aduan->loadMissing('lokasi');
        [$calon] = $this->calonTeknisi($aduan);
        $teknisi = $calon->firstWhere('id', (int) $request->input('teknisi_id'));

        if (! $teknisi) {
            return back()->with('error', __('Teknisi tidak sesuai dengan jurusan lokasi aduan.'));
        }

        $aduan->update(['teknisi_id' => $teknisi->id]);

        return back()->with('success', __('Teknisi :nama ditugaskan.', ['nama' => $teknisi->nama]));
    }

    public function setujui(Request $request, Aduan $aduan)
    {
        if (! $aduan->teknisi_id) {
            return back()->with('error', __('Tugaskan teknisi terlebih dahulu sebelum menyetujui aduan.'));
        }

        $aduan->update([
            'status' => 'approved',
            'catatan_admin' => $request->input('catatan_admin'),
            'diverifikasi_at' => now(),
        ]);

        return back()->with('success', __('Aduan :kode disetujui.', ['kode' => $aduan->kode_tiket]));
    }

    public function tolak(Request $request, Aduan $aduan)
    {
        $request->validate(['catatan_admin' => ['required', 'string']]);

        $aduan->update([
            'status' => 'rejected',
            'catatan_admin' => $request->input('catatan_admin'),
            'diverifikasi_at' => now(),
        ]);

        return back()->with('success', __('Aduan :kode ditolak.', ['kode' => $aduan->kode_tiket]));
    }

    public function selesai(Aduan $aduan)
    {
        abort_unless($aduan->status === 'approved', 403, __('Hanya aduan yang sudah disetujui yang dapat ditandai selesai.'));

        $aduan->update([
            'status' => 'selesai',
            'selesai_at' => now(),
        ]);

        return back()->with('success', __('Aduan :kode ditandai selesai.', ['kode' => $aduan->kode_tiket]));
    }

    public function destroy(Aduan $aduan)
    {
        $aduan->delete();

        return redirect()->route('admin.dashboard')->with('success', __('Tiket aduan dihapus.'));
    }
}