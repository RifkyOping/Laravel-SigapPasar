<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueLog extends Model
{
    protected $fillable = [
        'issue_id', 'user_id', 'action', 'status_dari', 'status_ke',
        'deskripsi', 'data_perubahan', 'ip_address',
    ];

    protected $casts = ['data_perubahan' => 'array'];

    public function issue() { return $this->belongsTo(Issue::class); }
    public function user()  { return $this->belongsTo(User::class); }
}
