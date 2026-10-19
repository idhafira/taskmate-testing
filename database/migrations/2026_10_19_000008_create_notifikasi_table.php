<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $t) {
            $t->increments('id_notif');
            $t->unsignedInteger('id_user');
            $t->unsignedInteger('id_task');
            $t->enum('jenis', ['penugasan', 'perubahan', 'h3', 'h1', 'hari_h', 'terlambat', 'review']);
            $t->string('pesan', 255);
            $t->boolean('status_baca')->default(false);
            $t->enum('status_email', ['terkirim', 'gagal', 'menunggu'])->default('menunggu');
            $t->dateTime('waktu_kirim');
            $t->index(['id_user', 'id_task', 'jenis']);
            $t->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
            $t->foreign('id_task')->references('id_task')->on('task')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
