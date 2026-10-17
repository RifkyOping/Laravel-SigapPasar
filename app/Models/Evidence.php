<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $table = 'evidences';

    protected $fillable = [
        'issue_id', 'uploaded_by', 'jenis', 'tahap',
        'file_path', 'file_name', 'file_type', 'file_size', 'deskripsi', 'metadata',
    ];

    protected $casts = ['metadata' => 'array'];

    public function issue()      { return $this->belongsTo(Issue::class); }
    public function uploadedBy() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) return '—';
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $i = 0;
        while ($size >= 1024 && $i < 3) { $size /= 1024; $i++; }
        return round($size, 1) . ' ' . $units[$i];
    }
}
