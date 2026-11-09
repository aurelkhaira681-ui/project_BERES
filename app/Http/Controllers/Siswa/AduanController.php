<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\Teknisi;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        // Pratinjau teknisi per lokasi (hanya tampilan; penugasan sebenarnya dihitung ulang di store())
        $format = fn (?Teknisi $t) => $t ? [
            'nama' => $t->nama,
            'jurusan' => Teknisi::singkat($t->jurusan),
            'inisial' => $t->inisial(),
            'aktif' => (int) $t->aktif_count,
            'bidang' => in_array($t->jurusan, Teknisi::BIDANG, true),
        ] : null;

        // Ruangan sisa / jurusan tanpa teknisi aktif -> teknisi umum (Pak Jiwanto)
        $umum = Teknisi::untukJurusan(Teknisi::BIDANG_SANITASI);

        $perJurusan = [];
        $penugasan = [];
        foreach ($lokasi as $l) {
            if (filled($l->jurusan) && ! array_key_exists($l->jurusan, $perJurusan)) {
                $perJurusan[$l->jurusan] = Teknisi::untukJurusan($l->jurusan);
            }
            $t = (filled($l->jurusan) ? $perJurusan[$l->jurusan] : null) ?? $umum;
            $penugasan[$l->id] = [
                'jurusan' => $l->jurusan ? Teknisi::singkat($l->jurusan) : null,
                'teknisi' => $format($t),
            ];
        }

        // Daftar lokasi dikelompokkan per lantai untuk <optgroup> di dropdown.
        $lokasiGrup = $lokasi->groupBy(fn ($l) => $l->lantai ?: 'Lainnya');

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

        // Teknisi ditentukan server berdasarkan lokasi (jurusan ruangan di denah); ruangan sisa -> Pak Jiwanto.
        // Siswa tidak bisa memilih / mengubahnya.
        $teknisi = Teknisi::untukAduan(
            Lokasi::find($data['lokasi_id'])?->jurusan,
        );

        $aduan = $request->user()->aduan()->create([
            ...$data,
            'teknisi_id' => $teknisi?->id,
            'foto' => $fotoPaths,
            'status' => 'pending',
        ]);

        // 'unduh_bukti' -> halaman detail otomatis memulai unduhan bukti laporan (PDF)
        return redirect()
            ->route('siswa.aduan.show', $aduan)
            ->with('success', __('Aduan :kode berhasil dikirim.', ['kode' => $aduan->kode_tiket]))
            ->with('unduh_bukti', true);
    }

    // Bukti laporan (PDF) milik siswa yang bersangkutan
    public function bukti(Aduan $aduan)
    {
        abort_unless($aduan->user_id === auth()->id(), 403);

        $aduan->load(['user', 'kategori', 'lokasi', 'teknisi']);

        // Foto disisipkan sebagai data URI supaya tidak bergantung pada symlink storage / URL publik.
        $foto = [];
        foreach (array_slice($aduan->foto ?? [], 0, 3) as $path) {
            $disk = Storage::disk('public');
            if ($disk->exists($path)) {
                $mime = $disk->mimeType($path) ?: 'image/jpeg';
                $foto[] = 'data:' . $mime . ';base64,' . base64_encode($disk->get($path));
            }
        }

        $pdf = Pdf::loadView('siswa.aduan.bukti-pdf', [
            'aduan' => $aduan,
            'foto' => $foto,
            'qr' => $this->qrTiket(route('siswa.aduan.show', $aduan)),
            'dicetak' => now()->timezone('Asia/Jakarta'),
        ])->setPaper('a4');

        return $pdf->download('Bukti-Laporan-' . $aduan->kode_tiket . '.pdf');
    }

    // QR code (data URI PNG) berisi tautan ke halaman status tiket. Null jika gagal dibuat -> PDF tetap jadi.
    private function qrTiket(string $url): ?string
    {
        try {
            return (new QRCode(new QROptions([
                'outputType' => QROutputInterface::GDIMAGE_PNG,
                'eccLevel' => 'M',
                'scale' => 6,
                'outputBase64' => true,
            ])))->render($url);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
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

        $aduan->hapusFile(); // hapus foto bukti dari penyimpanan
        $aduan->delete();

        return redirect()->route('siswa.riwayat')->with('success', __('Pengajuan dibatalkan.'));
    }
}