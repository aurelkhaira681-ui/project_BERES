<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = ['nama_ruangan', 'lantai'];

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        return $this->lantai ? "{$this->nama_ruangan} — {$this->lantai}" : $this->nama_ruangan;
    }
}
