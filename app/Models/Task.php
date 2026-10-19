<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public const STATUS = [
        'todo' => 'To-Do',
        'in_progress' => 'In Progress',
        'review' => 'Review',
        'done' => 'Done',
    ];

    protected $table = 'task';
    protected $primaryKey = 'id_task';

    protected $fillable = [
        'id_workspace', 'judul', 'deskripsi', 'status', 'deadline',
        'id_penanggung_jawab', 'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return ['deadline' => 'datetime'];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'id_workspace', 'id_workspace');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'id_penanggung_jawab', 'id_user');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');
    }

    public function logs()
    {
        return $this->hasMany(TaskStatusLog::class, 'id_task', 'id_task');
    }

    public function files()
    {
        return $this->hasMany(FileDokumen::class, 'id_task', 'id_task');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_task', 'id_task');
    }

    public function getTerlambatAttribute(): bool
    {
        return $this->status !== 'done' && $this->deadline->isPast();
    }
}
