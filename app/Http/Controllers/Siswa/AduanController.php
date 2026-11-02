<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class AduanController extends Controller
{
    // Halaman "Riwayat Aduan": filter status (Semua/Pending/Approved/Rejected), pencarian, dan pagination
    public function riwayat(Request $request)
    {
        $status = $request->query('status', 'semua');
        $q = trim((string) $request->query('q', ''));

        $query = $request->user()->aduan()->with(['kategori', 'lokasi'])->latest();

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode_tiket', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhereHas('kategori', fn ($k) => $k->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('lokasi', fn ($l) => $l->where('nama_ruangan', 'like', "%{$q}%"));
            });
        }

        $aduan = $query->paginate(10)->withQueryString();

        return view('siswa.riwayat', compact('aduan', 'status', 'q'));
    }

    // Form "Ajukan Aduan Baru"
    public function create()
    {
        $kategori = KategoriKerusakan::where('aktif', true)->get();
        $lokasi = Lokasi::orderBy('nama_ruangan')->get();

        return view('siswa.aduan.create', compact('kategori', 'lokasi'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_kerusakan_id' => ['required', 'exists:kategori_kerusakan,id'],
            'lokasi_id' => ['required', 'exists:lokasi,id'],
            'tanggal_kejadian' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'min:10'],
            'foto.*' => ['nullable', 'image', 'max:2048'],
        ]);

        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach (array_slice($request->file('foto'), 0, 3) as $file) {
                $fotoPaths[] = $file->store('bukti-aduan', 'public');
            }
        }

        $aduan = $request->user()->aduan()->create([
            ...$data,
            'foto' => $fotoPaths,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('siswa.aduan.show', $aduan)
            ->with('success', "Aduan {$aduan->kode_tiket} berhasil dikirim.");
    }

    // Halaman detail tiket + progres penanganan
    public function show(Aduan $aduan)
    {
        abort_unless($aduan->user_id === auth()->id(), 403);

        $aduan->load(['kategori', 'lokasi']);

        return view('siswa.aduan.show', compact('aduan'));
    }

    public function batalkan(Aduan $aduan)
    {
        abort_unless($aduan->user_id === auth()->id(), 403);
        abort_unless($aduan->status === 'pending', 403, 'Aduan yang sudah diverifikasi tidak dapat dibatalkan.');

        $aduan->delete();

        return redirect()->route('siswa.riwayat')->with('success', 'Pengajuan dibatalkan.');
    }
}