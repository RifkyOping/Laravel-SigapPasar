<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    protected $fillable = [
        'issue_id', 'ditutup_by', 'harga_sebelum_intervensi', 'harga_setelah_intervensi',
        'durasi_penanganan_hari', 'kesimpulan', 'catatan_akhir', 'status_akhir', 'tanggal_penutupan',
    ];

    protected $casts = [
        'tanggal_penutupan'          => 'datetime',
        'harga_sebelum_intervensi'   => 'decimal:2',
        'harga_setelah_intervensi'   => 'decimal:2',
    ];

    public function issue()    { return $this->belongsTo(Issue::class); }
    public function ditutupBy(){ return $this->belongsTo(User::class, 'ditutup_by'); }

    public function getPenurunanHargaAttribute(): ?float
    {
        if ($this->harga_sebelum_intervensi && $this->harga_setelah_intervensi) {
            return $this->harga_sebelum_intervensi - $this->harga_setelah_intervensi;
        }
        return null;
    }
}
