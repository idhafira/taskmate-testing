<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notif';
    public $timestamps = false;

    protected $fillable = ['id_user', 'id_task', 'jenis', 'pesan', 'status_baca', 'status_email', 'waktu_kirim'];

    protected function casts(): array
    {
        return [
            'status_baca' => 'boolean',
            'waktu_kirim' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'id_task', 'id_task');
    }
}
