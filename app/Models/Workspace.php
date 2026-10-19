<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workspace extends Model
{
    protected $table = 'workspace';
    protected $primaryKey = 'id_workspace';
    const UPDATED_AT = null;

    protected $fillable = ['nama_workspace', 'mata_kuliah', 'deskripsi', 'kode_undangan', 'id_pembuat'];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_pembuat', 'id_user');
    }

    public function members()
    {
        return $this->hasMany(WorkspaceMember::class, 'id_workspace', 'id_workspace');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'id_workspace', 'id_workspace');
    }

    public function files()
    {
        return $this->hasMany(FileDokumen::class, 'id_workspace', 'id_workspace');
    }

    public static function buatKode(): string
    {
        do {
            $kode = strtoupper(Str::random(8));
        } while (static::where('kode_undangan', $kode)->exists());

        return $kode;
    }

    public function totalUkuran(): int
    {
        return (int) FileVersion::whereIn('id_file', $this->files()->select('id_file'))->sum('ukuran_byte');
    }
}
