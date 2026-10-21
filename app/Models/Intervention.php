<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    protected $fillable = [
        'issue_id', 'dibuat_by', 'jenis_intervensi', 'nama_intervensi',
        'deskripsi', 'hasil_diharapkan', 'status',
        'tanggal_mulai', 'target_selesai', 'tanggal_selesai_aktual',
        'pic_nama', 'pic_jabatan', 'instansi_terlibat', 'progres_terakhir',
        'file_dokumen', 'nama_dokumen',
    ];

    protected $casts = [
        'tanggal_mulai'          => 'date',
        'target_selesai'         => 'date',
        'tanggal_selesai_aktual' => 'date',
        'instansi_terlibat'      => 'array',
    ];

    public function issue()    { return $this->belongsTo(Issue::class); }
    public function dibuatBy() { return $this->belongsTo(User::class, 'dibuat_by'); }

    public function hasDokumen(): bool
    {
        return !empty($this->file_dokumen);
    }
}
