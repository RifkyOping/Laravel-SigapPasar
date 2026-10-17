<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Issue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_isu', 'tanggal_temuan', 'platform', 'link_postingan', 'nama_akun',
        'jumlah_interaksi', 'jumlah_mention', 'kutipan_postingan',
        'komoditas_id', 'kategori_isu', 'pasar_id', 'lokasi_spesifik', 'kabupaten_id',
        'status', 'prioritas', 'virality_score',
        'input_by', 'verifikasi_by', 'assign_by',
        'catatan_input', 'is_merged', 'merged_to',
    ];

    protected $casts = [
        'tanggal_temuan' => 'datetime',
        'is_merged' => 'boolean',
        'virality_score' => 'float',
    ];

    // Scopes
    public function scopeByStatus($q, $status) { return $q->where('status', $status); }
    public function scopeByPrioritas($q, $p)   { return $q->where('prioritas', $p); }

    // Labels
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'baru'          => 'Baru',
            'diverifikasi'  => 'Diverifikasi',
            'ditugaskan'    => 'Ditugaskan',
            'di_lapangan'   => 'Di Lapangan',
            'intervensi'    => 'Intervensi',
            'selesai'       => 'Selesai',
            'ditutup'       => 'Ditutup',
            'hoaks'         => 'Hoaks',
            default         => $this->status,
        };
    }

    public function getPrioritasLabelAttribute(): string
    {
        return match($this->prioritas) {
            'rendah'  => 'Rendah',
            'sedang'  => 'Sedang',
            'tinggi'  => 'Tinggi',
            'kritis'  => 'Kritis',
            default   => $this->prioritas,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'baru'         => 'status-baru',
            'diverifikasi' => 'status-verifikasi',
            'ditugaskan'   => 'status-ditugaskan',
            'di_lapangan'  => 'status-lapangan',
            'intervensi'   => 'status-intervensi',
            'selesai'      => 'status-selesai',
            'hoaks'        => 'status-hoaks',
            default        => 'status-default',
        };
    }

    // Boot: auto-generate kode_isu
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($issue) {
            if (empty($issue->kode_isu)) {
                $prefix = 'ISU-' . now()->format('Ym') . '-';
                $count = static::whereYear('created_at', now()->year)
                               ->whereMonth('created_at', now()->month)
                               ->count() + 1;
                $issue->kode_isu = $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
            $issue->virality_score = ($issue->jumlah_interaksi + ($issue->jumlah_mention * 2));
        });
    }

    // Relationships
    public function komoditas()     { return $this->belongsTo(MasterData::class, 'komoditas_id'); }
    public function pasar()         { return $this->belongsTo(MasterData::class, 'pasar_id'); }
    public function kabupaten()     { return $this->belongsTo(MasterData::class, 'kabupaten_id'); }
    public function inputBy()       { return $this->belongsTo(User::class, 'input_by'); }
    public function verifikasiBy()  { return $this->belongsTo(User::class, 'verifikasi_by'); }
    public function assignBy()      { return $this->belongsTo(User::class, 'assign_by'); }
    public function verifications() { return $this->hasMany(IssueVerification::class); }
    public function assignments()   { return $this->hasMany(FieldAssignment::class); }
    public function activeAssignment() { return $this->hasOne(FieldAssignment::class)->latest(); }
    public function interventions() { return $this->hasMany(Intervention::class); }
    public function resolution()    { return $this->hasOne(Resolution::class); }
    public function evidences()     { return $this->hasMany(Evidence::class); }
    public function logs()          { return $this->hasMany(IssueLog::class)->latest(); }
    public function mergedTo()      { return $this->belongsTo(Issue::class, 'merged_to'); }
}
