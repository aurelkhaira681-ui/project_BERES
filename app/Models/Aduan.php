<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Aduan extends Model
{
    use HasFactory;

    protected $table = 'aduan';

    protected $fillable = [
        'kode_tiket',
        'user_id',
        'kategori_kerusakan_id',
        'lokasi_id',
        'teknisi_id',
        'tanggal_kejadian',
        'deskripsi',
        'foto',
        'status',
        'catatan_admin',
        'diverifikasi_at',
        'selesai_at',
    ];

    protected $casts = [
        'foto' => 'array',
        'tanggal_kejadian' => 'datetime',
        'diverifikasi_at' => 'datetime',
        'selesai_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Aduan $aduan) {
            if (empty($aduan->kode_tiket)) {
                $lastId = (int) (static::max('id') ?? 0) + 1;
                $aduan->kode_tiket = 'BRS-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKerusakan::class, 'kategori_kerusakan_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Teknisi::class, 'teknisi_id');
    }

    // Label & warna badge status, dipakai di semua view (mengikuti desain BERES)
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'selesai' => 'Selesai',
            default => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'selesai' => 'bg-blue-100 text-blue-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}