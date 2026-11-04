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
        'Mesin',
        'Rekayasa Perangkat Lunak',
        'Desain Pemodelan Infrastruktur Bangunan',
    ];

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

    /** Teknisi aktif di jurusan tersebut dengan beban paling ringan (null jika lokasi umum / tidak ada). */
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