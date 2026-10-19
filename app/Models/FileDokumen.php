<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileDokumen extends Model
{
    protected $table = 'file_dokumen';
    protected $primaryKey = 'id_file';
    const UPDATED_AT = null;

    protected $fillable = ['id_workspace', 'id_task', 'nama_file', 'dibuat_oleh'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'id_workspace', 'id_workspace');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'id_task', 'id_task');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');
    }

    public function versions()
    {
        return $this->hasMany(FileVersion::class, 'id_file', 'id_file')->orderByDesc('nomor_versi');
    }
}
