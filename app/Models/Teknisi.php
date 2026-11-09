<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teknisi extends Model
{
    use HasFactory;

    protected $table = 'teknisi';

    public const JURUSAN = [
        'Otomotif',
        'Otomasi',
        'Elektro',
        'Mesin',
        'Rekayasa Perangkat Lunak',
        'Desain Pemodelan Infrastruktur Bangunan',
    ];

    // Teknisi bidang: Pak Purdiyanto (ruangan Listrik) dan Pak Jiwanto (ruangan sisa yang tidak
    // terjangkau teknisi jurusan mana pun)
    public const BIDANG_LISTRIK = 'Listrik';
    public const BIDANG_SANITASI = 'Sanitasi & Umum';

    public const BIDANG = [self::BIDANG_LISTRIK, self::BIDANG_SANITASI];

    // Semua nilai yang boleh dipilih untuk teknisi (jurusan + bidang)
    public const SEMUA = [...self::JURUSAN, ...self::BIDANG];

    // Nilai yang boleh dipilih untuk lokasi (jurusan + ruangan Listrik). Kosong = ruangan sisa -> teknisi umum.
    public const LOKASI = [...self::JURUSAN, self::BIDANG_LISTRIK];

    protected $fillable = ['nama', 'jurusan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class, 'teknisi_id');
    }

    /** Tambahkan kolom aktif_count = jumlah aduan yang belum selesai ditangani. */
    public function scopeBeban(Builder $query): Builder
    {
        return $query->withCount(['aduan as aktif_count' => fn ($a) => $a->whereIn('status', ['pending', 'approved'])]);
    }

    /** Tambahkan rating_avg (rata-rata bintang) dan rating_count (jumlah penilaian) dari aduan yang sudah dinilai. */
    public function scopeDenganRating(Builder $query): Builder
    {
        return $query->withAvg(['aduan as rating_avg' => fn ($a) => $a->whereNotNull('rating')], 'rating')
            ->withCount(['aduan as rating_count' => fn ($a) => $a->whereNotNull('rating')]);
    }

    /** Teknisi aktif di jurusan/bidang tersebut dengan beban paling ringan (null jika tidak ada). */
    public static function untukJurusan(?string $jurusan): ?self
    {
        if (blank($jurusan)) {
            return null;
        }

        return static::where('aktif', true)
            ->where('jurusan', $jurusan)
            ->beban()
            ->orderBy('aktif_count')
            ->orderBy('id')
            ->first();
    }

    /**
     * Teknisi untuk sebuah aduan, ditentukan oleh LOKASI (jurusan ruangan di denah):
     * 1) ruangan milik jurusan/bidang tertentu -> teknisi di jurusan/bidang itu (beban paling ringan);
     * 2) ruangan sisa yang tidak terjangkau teknisi lain -> teknisi umum (Pak Jiwanto).
     * Kategori kerusakan tidak mempengaruhi penugasan.
     */
    public static function untukAduan(?string $jurusanLokasi): ?self
    {
        return static::untukJurusan($jurusanLokasi) ?? static::untukJurusan(self::BIDANG_SANITASI);
    }

    public static function singkat(?string $jurusan): string
    {
        return match ($jurusan) {
            'Rekayasa Perangkat Lunak' => 'RPL',
            'Desain Pemodelan Infrastruktur Bangunan' => 'DPIB',
            null, '' => 'Umum',
            default => $jurusan,
        };
    }

    public function inisial(): string
    {
        $kata = collect(explode(' ', $this->nama))->filter()->reject(fn ($k) => strtolower($k) === 'pak');

        return mb_strtoupper($kata->map(fn ($k) => mb_substr($k, 0, 1))->take(2)->implode('') ?: mb_substr($this->nama, 0, 1));
    }
}