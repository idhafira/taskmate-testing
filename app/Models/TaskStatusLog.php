<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatusLog extends Model
{
    protected $table = 'task_status_log';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = ['id_task', 'id_user', 'status_lama', 'status_baru', 'catatan', 'waktu'];

    protected function casts(): array
    {
        return ['waktu' => 'datetime'];
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'id_task', 'id_task');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
