<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    const UPDATED_AT = null;

    protected $fillable = ['nama', 'email', 'password', 'gagal_login', 'terkunci_sampai'];
    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'terkunci_sampai' => 'datetime',
        ];
    }

    // Tabel users pada ERD tidak memiliki kolom remember_token.
    public function getRememberTokenName()
    {
        return '';
    }

    public function keanggotaan()
    {
        return $this->hasMany(WorkspaceMember::class, 'id_user', 'id_user');
    }

    public function tugasDitugaskan()
    {
        return $this->hasMany(Task::class, 'id_penanggung_jawab', 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id_user');
    }
}
