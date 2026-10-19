<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceMember extends Model
{
    protected $table = 'workspace_member';
    protected $primaryKey = 'id_member';
    public $timestamps = false;

    protected $fillable = ['id_workspace', 'id_user', 'peran', 'tanggal_bergabung'];

    protected function casts(): array
    {
        return ['tanggal_bergabung' => 'datetime'];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'id_workspace', 'id_workspace');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
