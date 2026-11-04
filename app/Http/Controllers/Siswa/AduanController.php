<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\Teknisi;
use Illuminate\Http\Request;

class AduanController extends Controller
{
    // Halaman "Riwayat Aduan": filter status (Semua/Pending/Approved/Rejected), pencarian, dan pagination
    public function riwayat(Request $request)
    {
        $status = $request->query('status', 'semua');
        $q = trim((string) $request->query('q', ''));

        $query = $request->user()->aduan()->with(['kategori', 'lokasi', 'teknisi'])->latest();

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
        $urutan = ['Ruang Kelas', 'Laboratorium', 'Bengkel & Ruang Praktik', 'Kantor & Ruang Guru', 'Fasilitas Umum'];
        $lokasi = Lokasi::all()
            ->sortBy([
                fn ($a, $b) => (array_search($a->kelompok, $urutan) === false ? 99 : array_search($a->kelompok, $urutan))
                    <=> (array_search($b->kelompok, $urutan) === false ? 99 : array_search($b->kelompok, $urutan)),
                fn ($a, $b) => strnatcasecmp($a->nama_ruangan, $b->nama_ruangan),
            ])
            ->values();
        $lokasiGrup = $lokasi->groupBy(fn ($l) => $l->kelompok ?: 'Lainnya');

        // Pratinjau teknisi per lokasi (hanya tampilan; penugasan sebenarnya dihitung ulang di store())
        $perJurusan = [];
        $penugasan = [];
        foreach ($lokasi as $l) {
            if (filled($l->jurusan) && ! array_key_exists($l->jurusan, $perJurusan)) {
                $perJurusan[$l->jurusan] = Teknisi::untukJurusan($l->jurusan);
            }
            $t = filled($l->jurusan) ? $perJurusan[$l->jurusan] : null;
            $penugasan[$l->id] = [
                'jurusan' => $l->jurusan ? Teknisi::singkat($l->jurusan) : null,
                'teknisi' => $t ? [
                    'nama' => $t->nama,
                    'jurusan' => Teknisi::singkat($t->jurusan),
                    'inisial' => $t->inisial(),
                    'aktif' => (int) $t->aktif_count,
                ] : null,
            ];
        }

        return view('siswa.aduan.create', compact('kategori', 'lokasi', 'lokasiGrup', 'penugasan'));
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

        // Teknisi ditentukan server berdasarkan jurusan lokasi — siswa tidak bisa memilih / mengubahnya
        $teknisi = Teknisi::untukJurusan(Lokasi::find($data['lokasi_id'])?->jurusan);

        $aduan = $request->user()->aduan()->create([
            ...$data,
            'teknisi_id' => $teknisi?->id,
            'foto' => $fotoPaths,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('siswa.aduan.show', $aduan)
            ->with('success', __('Aduan :kode berhasil dikirim.', ['kode' => $aduan->kode_tiket]));
    }

    // Halaman detail tiket + progres penanganan
    public function show(Aduan $aduan)
    {
        abort_unless($aduan->user_id === auth()->id(), 403);

        $aduan->load(['kategori', 'lokasi', 'teknisi']);

        return view('siswa.aduan.show', compact('aduan'));
    }

    public function batalkan(Aduan $aduan)
    {
        abort_unless($aduan->user_id === auth()->id(), 403);
        abort_unless($aduan->status === 'pending', 403, __('Aduan yang sudah diverifikasi tidak dapat dibatalkan.'));

        $aduan->delete();

        return redirect()->route('siswa.riwayat')->with('success', __('Pengajuan dibatalkan.'));
    }
}