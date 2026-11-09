<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
        'nisn',
        'kelas',
        'no_presensi',
        'konsentrasi_keahlian',
        'no_hp',
        'foto',
        'notif_dibaca_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notif_dibaca_at' => 'datetime',
        ];
    }

    /** URL foto profil (null jika belum ada). Parameter ?v= memaksa browser memuat ulang saat foto diganti. */
    public function fotoUrl(): ?string
    {
        return $this->foto
            ? asset('storage/' . $this->foto) . '?v=' . ($this->updated_at?->timestamp ?? 0)
            : null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class);
    }

    /**
     * Notifikasi status untuk siswa: aduan yang sudah disetujui / sedang dikerjakan / selesai / ditolak
     * dan berubah setelah lonceng terakhir dibuka (maksimal 14 hari ke belakang).
     */
    public function notifikasiBelumDibaca()
    {
        $batas = now()->subDays(14);
        $sejak = $this->notif_dibaca_at && $this->notif_dibaca_at->gt($batas) ? $this->notif_dibaca_at : $batas;

        return $this->aduan()
            ->where('status', '!=', 'pending')
            ->where('updated_at', '>', $sejak);
    }
}