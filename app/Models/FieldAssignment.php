<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldAssignment extends Model
{
    protected $fillable = [
        'issue_id', 'petugas_id', 'assigned_by', 'tanggal_penugasan', 'tanggal_kunjungan',
        'status', 'metode_verifikasi', 'no_telepon_sumber', 'sosmed_sumber',
        'catatan_temuan', 'foto_bukti', 'latitude', 'longitude',
        'harga_aktual', 'catatan_wawancara', 'checkin_at', 'catatan_admin',
    ];

    protected $casts = [
        'tanggal_penugasan' => 'datetime',
        'tanggal_kunjungan' => 'datetime',
        'checkin_at'        => 'datetime',
        'latitude'          => 'float',
        'longitude'         => 'float',
        'harga_aktual'      => 'decimal:2',
    ];

    public function issue()      { return $this->belongsTo(Issue::class); }
    public function petugas()    { return $this->belongsTo(User::class, 'petugas_id'); }
    public function assignedBy() { return $this->belongsTo(User::class, 'assigned_by'); }
}
