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
}