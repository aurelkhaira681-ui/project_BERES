<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKerusakan extends Model
{
    use HasFactory;

    protected $table = 'kategori_kerusakan';

    protected $fillable = ['nama', 'icon', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class, 'kategori_kerusakan_id');
    }
}
