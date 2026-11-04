<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Daftar resmi NISN siswa. NISN yang tidak ada di sini tidak bisa mendaftar maupun masuk. */
class SiswaTerdaftar extends Model
{
    protected $table = 'siswa_terdaftar';

    protected $fillable = ['nisn', 'nama', 'kelas', 'nipd'];

    /** Samakan format: hanya angka, 10 digit (lengkapi 0 di depan). */
    public static function normalisasi(?string $nisn): string
    {
        $angka = preg_replace('/\D/', '', (string) $nisn);

        return $angka === '' ? '' : str_pad($angka, 10, '0', STR_PAD_LEFT);
    }

    public static function cari(?string $nisn): ?self
    {
        $n = static::normalisasi($nisn);

        return $n === '' ? null : static::where('nisn', $n)->first();
    }
}