<?php

namespace Database\Seeders;

use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\SiswaTerdaftar;
use App\Models\Teknisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Lokasi dari denah sekolah + daftar NISN siswa resmi
        $this->call([TeknisiBidangSeeder::class, LokasiDenahSeeder::class, SiswaTerdaftarSeeder::class]);

        // Akun Admin/Petugas
        $admin = User::create([
            'role' => 'admin',
            'name' => 'Aurel Khaira Lubna',
            'email' => 'admin@smkn5solo.sch.id',
            'password' => Hash::make('password'),
        ]);

        // Akun Siswa contoh (NISN harus ada di daftar siswa resmi)
        $resmi = SiswaTerdaftar::orderBy('id')->first();
        $siswa = User::create([
            'role' => 'siswa',
            'name' => $resmi->nama,
            'email' => 'siswa.contoh@siswa.sch.id',
            'password' => Hash::make('password'),
            'nisn' => $resmi->nisn,
            'kelas' => $resmi->kelas,
            'no_presensi' => '1',
            'konsentrasi_keahlian' => 'Desain Pemodelan Infrastruktur Bangunan',
            'no_hp' => '0812-3456-7890',
        ]);

        $kategori = collect([
            ['nama' => 'Elektronik', 'icon' => '💡'],
            ['nama' => 'Sanitasi', 'icon' => '🚿'],
            ['nama' => 'Furnitur', 'icon' => '🪑'],
            ['nama' => 'Lainnya', 'icon' => '🧩'],
        ])->map(fn ($item) => KategoriKerusakan::create($item));

        // Daftar ruangan diambil dari denah sekolah (config/denah.php)
        $this->call(LokasiDenahSeeder::class);

        $lokasi = collect(['Lab. RPL 1', 'WC (dekat Lab. RPL 4)', 'Rg 1.07'])
            ->map(fn ($nama) => Lokasi::where('nama_ruangan', $nama)->firstOrFail());

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[0]->id,
            'lokasi_id' => $lokasi[0]->id,
            'teknisi_id' => Teknisi::untukAduan($lokasi[0]->jurusan)?->id,
            'tanggal_kejadian' => now()->subDays(2),
            'deskripsi' => 'AC di sisi kanan ruangan mengeluarkan suara kasar dan tidak mengeluarkan udara dingin sejak Senin pagi.',
            'status' => 'pending',
        ]);

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[1]->id,
            'lokasi_id' => $lokasi[1]->id,
            'teknisi_id' => Teknisi::untukAduan($lokasi[1]->jurusan)?->id,
            'tanggal_kejadian' => now()->subDays(6),
            'deskripsi' => 'Kran wastafel toilet putri bocor terus menerus.',
            'status' => 'approved',
            'diverifikasi_at' => now()->subDays(5),
        ]);

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[0]->id,
            'lokasi_id' => $lokasi[2]->id,
            'teknisi_id' => Teknisi::untukAduan($lokasi[2]->jurusan)?->id,
            'tanggal_kejadian' => now()->subDays(12),
            'deskripsi' => 'Proyektor di ruang kelas redup dan sulit terbaca.',
            'status' => 'rejected',
            'catatan_admin' => 'Sudah dicek, proyektor masih normal. Silakan cek pengaturan kecerahan terlebih dahulu.',
            'diverifikasi_at' => now()->subDays(10),
        ]);

        $this->aduanDemo($kategori);
    }

    /**
     * Data demo: beberapa siswa + puluhan aduan dengan status campur dan tanggal tersebar
     * ±5 bulan ke belakang, supaya dashboard, grafik, dan donut admin tidak kosong.
     * Dibuat deterministik (mt_srand) agar hasilnya sama setiap kali seeding.
     */
    private function aduanDemo($kategori): void
    {
        mt_srand(2026);

        $siswaDemo = SiswaTerdaftar::orderBy('id')->skip(1)->take(5)->get()->map(fn ($r, $i) => User::create([
            'role' => 'siswa',
            'name' => $r->nama,
            'email' => 'siswa' . ($i + 2) . '@siswa.sch.id',
            'password' => Hash::make('password'),
            'nisn' => $r->nisn,
            'kelas' => $r->kelas,
            'no_presensi' => (string) ($i + 2),
            'no_hp' => '0812-0000-' . str_pad((string) ($i + 2), 4, '0', STR_PAD_LEFT),
        ]));

        // [indeks kategori, deskripsi] -> 0 Elektronik, 1 Sanitasi, 2 Furnitur, 3 Lainnya
        $masalah = [
            [0, 'Lampu neon berkedip-kedip dan sesekali mati sendiri.'],
            [0, 'Stop kontak di dinding belakang tidak ada aliran listrik.'],
            [0, 'Kipas angin langit-langit berbunyi keras saat dinyalakan.'],
            [0, 'Proyektor tidak menampilkan gambar, hanya layar biru.'],
            [0, 'AC tidak dingin dan meneteskan air ke lantai.'],
            [1, 'Kran wastafel bocor dan air terus menetes.'],
            [1, 'Toilet tersumbat dan air tidak mengalir ke bawah.'],
            [1, 'Gayung dan selang air di toilet rusak.'],
            [1, 'Lantai kamar mandi menggenang karena saluran buntu.'],
            [2, 'Kaki kursi patah, berbahaya kalau diduduki.'],
            [2, 'Meja belajar goyang dan permukaannya terkelupas.'],
            [2, 'Papan tulis retak di bagian pojok kanan atas.'],
            [2, 'Engsel pintu lemari penyimpanan lepas.'],
            [3, 'Kaca jendela retak di sisi dekat pintu.'],
            [3, 'Atap bocor saat hujan, air jatuh di atas meja siswa.'],
            [3, 'Gagang pintu kelas longgar dan sulit dikunci.'],
        ];
        $catatanAdmin = [
            'Sudah dicatat, teknisi akan mengecek lokasi.',
            'Disetujui. Perbaikan dijadwalkan dalam minggu ini.',
            'Terima kasih laporannya, segera ditindaklanjuti.',
        ];
        $catatanPerbaikan = [
            'Komponen diganti baru dan sudah diuji berfungsi normal.',
            'Sudah diperbaiki dan dibersihkan, ruangan aman dipakai.',
            'Perbaikan selesai, mohon dilaporkan lagi bila masih bermasalah.',
        ];
        $lokasiSemua = Lokasi::orderBy('id')->get();

        // Komposisi status: rejected dan pending lebih sedikit dari yang selesai
        $komposisi = array_merge(
            array_fill(0, 14, 'selesai'),
            array_fill(0, 5, 'approved'),
            array_fill(0, 5, 'pending'),
            array_fill(0, 3, 'rejected'),
        );
        shuffle($komposisi);

        $pelapor = $siswaDemo->all();

        foreach ($komposisi as $i => $status) {
            [$k, $deskripsi] = $masalah[$i % count($masalah)];
            $lokasi = $lokasiSemua[mt_rand(0, $lokasiSemua->count() - 1)];

            // Aduan selesai/ditolak lebih lama; pending paling baru
            $umurHari = match ($status) {
                'selesai' => mt_rand(8, 150),
                'rejected' => mt_rand(8, 120),
                'approved' => mt_rand(2, 14),
                default => mt_rand(0, 4),
            };
            $dibuat = now()->subDays($umurHari)->setTime(mt_rand(7, 14), mt_rand(0, 59));

            $aduan = Aduan::create([
                'user_id' => $pelapor[$i % count($pelapor)]->id,
                'kategori_kerusakan_id' => $kategori[$k]->id,
                'lokasi_id' => $lokasi->id,
                'teknisi_id' => Teknisi::untukAduan($lokasi->jurusan)?->id,
                'tanggal_kejadian' => $dibuat->copy()->subDays(mt_rand(0, 2)),
                'deskripsi' => $deskripsi,
                'status' => $status,
                'catatan_admin' => match ($status) {
                    'rejected' => 'Setelah dicek, laporan tidak sesuai dengan kondisi di lokasi.',
                    'pending' => null,
                    default => $catatanAdmin[array_rand($catatanAdmin)],
                },
                'diverifikasi_at' => $status === 'pending' ? null : $dibuat->copy()->addHours(mt_rand(3, 30)),
                'selesai_at' => $status === 'selesai' ? $dibuat->copy()->addDays(mt_rand(2, 6)) : null,
                'catatan_perbaikan' => $status === 'selesai' ? $catatanPerbaikan[array_rand($catatanPerbaikan)] : null,
            ]);

            $aduan->forceFill(['created_at' => $dibuat, 'updated_at' => $aduan->selesai_at ?? $aduan->diverifikasi_at ?? $dibuat])->saveQuietly();
        }
    }
}