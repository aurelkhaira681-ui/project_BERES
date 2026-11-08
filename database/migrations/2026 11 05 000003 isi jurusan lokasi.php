<?php

use App\Models\Lokasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Melengkapi jurusan/bidang tiap lokasi supaya aduan tidak menumpuk di Pak Jiwanto.
//
// Pembagian teknisi:
//   Pak Indri & Pak Indri Ruby -> Otomotif     Pak Heru       -> Elektro
//   Pak Joko                   -> Otomasi      Pak Sarpono    -> Mesin
//   Pak Riki & Pak Fajar       -> RPL (termasuk Lab/T. ASM, Axioo, KKPI, Lab. MM)
//   Pak Tanto                  -> DPIB         Pak Purdiyanto -> Listrik
//   Pak Jiwanto                -> hanya ruangan sisa (kelas biasa, kantor, WC, fasilitas umum)
//
// Hanya mengisi jurusan yang masih kosong, jadi pengaturan manual di Master Data tidak tertimpa.
// (Migration lama "2026 11 05 000003 isi jurusan lokasi.php" bernama pakai spasi dan boleh dihapus;
//  aturannya sudah dimasukkan ke sini lewat Lokasi::jurusanDariNama.)
return new class extends Migration
{
    public function up(): void
    {
        // WC / toilet selalu ruangan sisa, walau namanya menyebut lab tertentu.
        DB::table('lokasi')
            ->where(fn ($q) => $q->where('nama_ruangan', 'like', 'WC%')->orWhere('nama_ruangan', 'like', 'Toilet%'))
            ->update(['jurusan' => null]);

        // Isi jurusan yang masih kosong berdasarkan nama ruangan.
        foreach (DB::table('lokasi')->whereNull('jurusan')->get() as $lokasi) {
            $jurusan = Lokasi::jurusanDariNama($lokasi->nama_ruangan);

            if ($jurusan) {
                DB::table('lokasi')->where('id', $lokasi->id)->update(['jurusan' => $jurusan]);
            }
        }

        // Aduan yang masih berjalan dan terlanjur dipegang Pak Jiwanto, padahal ruangannya kini
        // punya teknisi jurusan -> pindahkan ke teknisi jurusan itu (yang bebannya paling ringan).
        $umum = DB::table('teknisi')->where('jurusan', 'Sanitasi & Umum')->pluck('id');
        $status = ['pending', 'approved'];

        $perluPindah = DB::table('aduan')
            ->join('lokasi', 'lokasi.id', '=', 'aduan.lokasi_id')
            ->whereIn('aduan.status', $status)
            ->whereIn('aduan.teknisi_id', $umum)
            ->whereNotNull('lokasi.jurusan')
            ->get(['aduan.id', 'lokasi.jurusan']);

        foreach ($perluPindah as $item) {
            $tujuan = DB::table('teknisi')
                ->where('aktif', true)
                ->where('jurusan', $item->jurusan)
                ->get()
                ->sortBy(fn ($t) => DB::table('aduan')->where('teknisi_id', $t->id)->whereIn('status', $status)->count())
                ->first();

            if ($tujuan) {
                DB::table('aduan')->where('id', $item->id)->update(['teknisi_id' => $tujuan->id]);
            }
        }
    }

    public function down(): void
    {
        // Tidak ada yang perlu dikembalikan.
    }
};
