<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterData extends Model
{
    use HasFactory;

    protected $table = 'master_data';

    protected $fillable = [
        'type', 'name', 'code', 'satuan', 'harga_referensi', 'deskripsi', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'harga_referensi' => 'decimal:2',
    ];

    public function scopeKomoditas($q) { return $q->where('type', 'komoditas')->where('is_active', true); }
    public function scopeKabupaten($q) { return $q->where('type', 'kabupaten')->where('is_active', true); }
    public function scopePasar($q)     { return $q->where('type', 'pasar')->where('is_active', true); }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'kabupaten_id');
    }
}
