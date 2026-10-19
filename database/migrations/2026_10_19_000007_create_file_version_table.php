<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_version', function (Blueprint $t) {
            $t->increments('id_version');
            $t->unsignedInteger('id_file');
            $t->unsignedInteger('nomor_versi');
            $t->string('path_file', 255);
            $t->unsignedBigInteger('ukuran_byte');
            $t->string('catatan_revisi', 255)->nullable();
            $t->unsignedInteger('id_uploader');
            $t->dateTime('waktu_unggah');
            $t->unique(['id_file', 'nomor_versi']);
            $t->foreign('id_file')->references('id_file')->on('file_dokumen')->cascadeOnDelete();
            $t->foreign('id_uploader')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_version');
    }
};
