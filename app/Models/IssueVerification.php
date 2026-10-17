<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueVerification extends Model
{
    protected $fillable = [
        'issue_id', 'verifikator_id', 'hasil_verifikasi', 'prioritas_rekomendasi',
        'catatan_verifikasi', 'saran_tindakan', 'duplikasi_dari', 'tanggal_verifikasi',
    ];

    protected $casts = ['tanggal_verifikasi' => 'datetime'];

    public function issue()         { return $this->belongsTo(Issue::class); }
    public function verifikator()   { return $this->belongsTo(User::class, 'verifikator_id'); }
    public function duplikasiDari() { return $this->belongsTo(Issue::class, 'duplikasi_dari'); }
}
