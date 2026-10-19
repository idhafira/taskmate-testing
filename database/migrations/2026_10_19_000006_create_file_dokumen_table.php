<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_dokumen', function (Blueprint $t) {
            $t->increments('id_file');
            $t->unsignedInteger('id_workspace');
            $t->unsignedInteger('id_task')->nullable();
            $t->string('nama_file', 150);
            $t->unsignedInteger('dibuat_oleh');
            $t->dateTime('created_at')->nullable();
            $t->unique(['id_workspace', 'nama_file']);
            $t->foreign('id_workspace')->references('id_workspace')->on('workspace')->cascadeOnDelete();
            $t->foreign('id_task')->references('id_task')->on('task')->nullOnDelete();
            $t->foreign('dibuat_oleh')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_dokumen');
    }
};
