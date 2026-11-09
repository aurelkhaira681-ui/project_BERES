<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
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
        'foto_selesai',
        'status',
        'prioritas',
        'rating',
        'ulasan',
        'dinilai_at',
        'catatan_admin',
        'catatan_perbaikan',
        'diverifikasi_at',
        'selesai_at',
    ];

    protected $casts = [
        'foto' => 'array',
        'foto_selesai' => 'array',
        'tanggal_kejadian' => 'datetime',
        'diverifikasi_at' => 'datetime',
        'selesai_at' => 'datetime',
        'dinilai_at' => 'datetime',
    ];

    public const PRIORITAS = ['rendah', 'sedang', 'mendesak'];

    protected static function booted(): void
    {
        // Kode tiket sementara dulu (kolom wajib unik), lalu diganti kode final
        // berdasarkan id asli supaya tidak bentrok kalau dua aduan masuk bersamaan.
        static::creating(function (Aduan $aduan) {
            if (empty($aduan->kode_tiket)) {
                $aduan->kode_tiket = 'TMP-' . Str::uuid();
            }
        });

        static::created(function (Aduan $aduan) {
            if (str_starts_with($aduan->kode_tiket, 'TMP-')) {
                $aduan->kode_tiket = 'BRS-' . str_pad($aduan->id, 4, '0', STR_PAD_LEFT);
                $aduan->saveQuietly();
            }
        });
    }

    /** Hapus semua file foto (bukti aduan + bukti perbaikan) milik tiket ini dari storage. */
    public function hapusFile(): void
    {
        $files = array_merge($this->foto ?? [], $this->foto_selesai ?? []);

        if ($files) {
            Storage::disk('public')->delete($files);
        }
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

    // ===== Prioritas / urgensi =====

    /** Urutkan: Mendesak -> Sedang -> Rendah, lalu yang terbaru. Dipakai di dashboard & daftar admin. */
    public function scopeUrutPrioritas($query)
    {
        return $query->orderByRaw("CASE prioritas WHEN 'mendesak' THEN 0 WHEN 'sedang' THEN 1 ELSE 2 END")->latest();
    }

    /** Aduan yang belum selesai ditangani (menunggu / diproses). */
    public function scopeBelumSelesai($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    public function prioritasLabel(): string
    {
        return ucfirst($this->prioritas ?: 'sedang');
    }

    public function isMendesak(): bool
    {
        return $this->prioritas === 'mendesak';
    }

    /** Aduan sudah selesai dan siswa belum memberi bintang. */
    public function bisaDinilai(): bool
    {
        return $this->status === 'selesai' && $this->rating === null;
    }
}