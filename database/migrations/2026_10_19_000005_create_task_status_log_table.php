<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_status_log', function (Blueprint $t) {
            $t->increments('id_log');
            $t->unsignedInteger('id_task');
            $t->unsignedInteger('id_user');
            $t->enum('status_lama', ['todo', 'in_progress', 'review', 'done']);
            $t->enum('status_baru', ['todo', 'in_progress', 'review', 'done']);
            $t->string('catatan', 255)->nullable();
            $t->dateTime('waktu');
            $t->foreign('id_task')->references('id_task')->on('task')->cascadeOnDelete();
            $t->foreign('id_user')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_status_log');
    }
};
