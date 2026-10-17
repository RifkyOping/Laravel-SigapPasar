<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'nip', 'instansi', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'pimpinan'                      => 'Pimpinan',
            'super_admin', 'admin'          => 'Admin',
            'pengawas_siber', 'pengawas'    => 'Pengawas',
            'verifikator'                   => 'Verifikator',
            'petugas_lapangan', 'petugas'   => 'Petugas',
            default                         => 'Unknown',
        };
    }

    public function isRole(string|array $roles): bool
    {
        $arr = (array) $roles;
        if (in_array($this->role, $arr)) {
            return true;
        }
        if (($this->role === 'super_admin' && in_array('admin', $arr)) ||
            ($this->role === 'admin' && in_array('super_admin', $arr))) {
            return true;
        }
        if (($this->role === 'pengawas_siber' && in_array('pengawas', $arr)) ||
            ($this->role === 'pengawas' && in_array('pengawas_siber', $arr))) {
            return true;
        }
        if (($this->role === 'petugas_lapangan' && in_array('petugas', $arr)) ||
            ($this->role === 'petugas' && in_array('petugas_lapangan', $arr))) {
            return true;
        }
        return false;
    }

    public function isPimpinan(): bool        { return $this->role === 'pimpinan'; }
    public function isSuperAdmin(): bool      { return in_array($this->role, ['super_admin', 'admin']); }
    public function isAdmin(): bool           { return in_array($this->role, ['super_admin', 'admin']); }
    public function isPengawasSiber(): bool   { return in_array($this->role, ['pengawas_siber', 'pengawas']); }
    public function isPengawas(): bool        { return in_array($this->role, ['pengawas_siber', 'pengawas']); }
    public function isVerifikator(): bool     { return $this->role === 'verifikator'; }
    public function isPetugasLapangan(): bool { return in_array($this->role, ['petugas_lapangan', 'petugas']); }
    public function isPetugas(): bool         { return in_array($this->role, ['petugas_lapangan', 'petugas']); }

    // Relationships
    public function issuesInput()    { return $this->hasMany(Issue::class, 'input_by'); }
    public function fieldAssignments(){ return $this->hasMany(FieldAssignment::class, 'petugas_id'); }
    public function issueLogs()      { return $this->hasMany(IssueLog::class); }
}
