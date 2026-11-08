<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = ['kode', 'nama_ruangan', 'lantai', 'kelompok', 'jurusan'];

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        return $this->lantai ? "{$this->nama_ruangan} — {$this->lantai}" : $this->nama_ruangan;
    }

    /**
     * Aturan penentuan jurusan/bidang dari nama ruangan (urutan = prioritas, yang cocok pertama dipakai).
     * Ruangan yang tidak cocok dengan aturan mana pun = ruangan sisa -> teknisi umum (Pak Jiwanto).
     *
     *  Listrik (Pak Purdiyanto)   : bengkel & ruang guru Listrik, Bkl DLE
     *  Elektro (Pak Heru)         : RPS Elektronika, Bkl Elektro 1-4, R. Guru (Elektronika)
     *  Otomotif (Pak Indri / Indri Ruby): RPS Otomotif, TKR, TSM
     *  Mesin (Pak Sarpono)        : RPS Teknik Pemesinan
     *  RPL (Pak Riki / Pak Fajar) : Lab RPL, Lab/T. ASM, Axioo, KKPI/KKP, Lab. MM, R. Guru PPLG
     *  DPIB (Pak Tanto)           : ruang gambar & lab komputer DPIB, RPS TKP
     *  Otomasi (Pak Joko)         : Bengkel Otomasi, SKE, PSA, SKT, SKEE
     */
    public const ATURAN_JURUSAN = [
        'Listrik' => ['/listr/', '/\\bdle\\b/'],
        'Elektro' => ['/elektro/', '/elektronika/', '/^r\\. guru$/'],
        'Otomotif' => ['/\\btsm\\b|\\btsm[ .\\d]/', '/\\btkr\\b/', '/otomotif/'],
        'Mesin' => ['/pemesinan/'],
        'Desain Pemodelan Infrastruktur Bangunan' => ['/dpib/', '/\\btkp\\b/', '/r\\. gbr/'],
        'Rekayasa Perangkat Lunak' => ['/\\brpl\\b/', '/\\basm\\b/', '/axioo/', '/kkpi/', '/\\bkkp\\b/', '/pplg/', '/lab\\. mm\\b/'],
        'Otomasi' => ['/otomasi/', '/\\b(ske|skee|psa|skt)\\b/'],
    ];

    /** Jurusan/bidang untuk sebuah nama ruangan (null = ruangan sisa / umum). */
    public static function jurusanDariNama(?string $nama): ?string
    {
        $nama = mb_strtolower(trim((string) $nama));

        // WC / toilet selalu ruangan sisa (Pak Jiwanto), walau namanya menyebut lab tertentu
        if (preg_match('/^(wc|toilet)/', $nama)) {
            return null;
        }

        foreach (self::ATURAN_JURUSAN as $jurusan => $pola) {
            foreach ($pola as $regex) {
                if (preg_match($regex, $nama)) {
                    return $jurusan;
                }
            }
        }

        return null;
    }
}
