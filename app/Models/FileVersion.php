<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileVersion extends Model
{
    protected $table = 'file_version';
    protected $primaryKey = 'id_version';
    public $timestamps = false;

    protected $fillable = [
        'id_file', 'nomor_versi', 'path_file', 'ukuran_byte',
        'catatan_revisi', 'id_uploader', 'waktu_unggah',
    ];

    protected function casts(): array
    {
        return ['waktu_unggah' => 'datetime'];
    }

    public function file()
    {
        return $this->belongsTo(FileDokumen::class, 'id_file', 'id_file');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'id_uploader', 'id_user');
    }
}
