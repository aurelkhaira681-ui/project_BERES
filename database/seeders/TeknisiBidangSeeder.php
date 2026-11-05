<?php

namespace Database\Seeders;

use App\Models\Teknisi;
use Illuminate\Database\Seeder;

class TeknisiBidangSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['Pak Indri',        'Otomotif'],
            ['Pak Indri Ruby',   'Otomotif'],
            ['Pak Heru',         'Elektro'],
            ['Pak Joko',         'Otomasi'],
            ['Pak Sarpono',      'Mesin'],
            ['Pak Riki',         'Rekayasa Perangkat Lunak'],
            ['Pak Fajar',        'Rekayasa Perangkat Lunak'],
            ['Pak Tanto',        'Desain Pemodelan Infrastruktur Bangunan'],
            ['Pak Purdiyanto',   Teknisi::BIDANG_LISTRIK],   // kategori Listrik
            ['Pak Jiwanto',      Teknisi::BIDANG_SANITASI],  // kategori Sanitasi/Umum + lokasi tanpa teknisi jurusan
        ];

        foreach ($daftar as [$nama, $jurusan]) {
            Teknisi::updateOrCreate(['nama' => $nama], ['jurusan' => $jurusan, 'aktif' => true]);
        }
    }
}