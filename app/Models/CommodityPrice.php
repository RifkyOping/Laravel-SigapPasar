<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommodityPrice extends Model
{
    use HasFactory;

    protected $table = 'commodity_prices';

    protected $fillable = [
        'komoditas_id',
        'nama_variant',
        'satuan',
        'tanggal',
        'harga',
        'sumber',
        'created_by',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function setTanggalAttribute($value): void
    {
        $this->attributes['tanggal'] = $value instanceof \DateTimeInterface
            ? $value->format('Y-m-d')
            : \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    public function getTanggalAttribute($value): ?\Carbon\Carbon
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    public function getSumberLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', (string) $this->sumber));
    }

    public function komoditas()
    {
        return $this->belongsTo(MasterData::class, 'komoditas_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
