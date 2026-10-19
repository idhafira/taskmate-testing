<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task', function (Blueprint $t) {
            $t->increments('id_task');
            $t->unsignedInteger('id_workspace');
            $t->string('judul', 150);
            $t->text('deskripsi')->nullable();
            $t->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo');
            $t->dateTime('deadline');
            $t->unsignedInteger('id_penanggung_jawab');
            $t->unsignedInteger('dibuat_oleh');
            $t->dateTime('created_at')->nullable();
            $t->dateTime('updated_at')->nullable();
            $t->index(['status', 'deadline']);
            $t->foreign('id_workspace')->references('id_workspace')->on('workspace')->cascadeOnDelete();
            $t->foreign('id_penanggung_jawab')->references('id_user')->on('users');
            $t->foreign('dibuat_oleh')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task');
    }
};
