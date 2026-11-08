<?php

namespace Database\Seeders;

use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\SiswaTerdaftar;
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
            'tanggal_kejadian' => now()->subDays(2),
            'deskripsi' => 'AC di sisi kanan ruangan mengeluarkan suara kasar dan tidak mengeluarkan udara dingin sejak Senin pagi.',
            'status' => 'pending',
        ]);

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[1]->id,
            'lokasi_id' => $lokasi[1]->id,
            'tanggal_kejadian' => now()->subDays(6),
            'deskripsi' => 'Kran wastafel toilet putri bocor terus menerus.',
            'status' => 'approved',
            'diverifikasi_at' => now()->subDays(5),
        ]);

        Aduan::create([
            'user_id' => $siswa->id,
            'kategori_kerusakan_id' => $kategori[0]->id,
            'lokasi_id' => $lokasi[2]->id,
            'tanggal_kejadian' => now()->subDays(12),
            'deskripsi' => 'Proyektor di ruang kelas redup dan sulit terbaca.',
            'status' => 'rejected',
            'catatan_admin' => 'Sudah dicek, proyektor masih normal. Silakan cek pengaturan kecerahan terlebih dahulu.',
            'diverifikasi_at' => now()->subDays(10),
        ]);
    }
}
