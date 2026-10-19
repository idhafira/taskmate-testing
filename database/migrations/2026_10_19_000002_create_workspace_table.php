<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace', function (Blueprint $t) {
            $t->increments('id_workspace');
            $t->string('nama_workspace', 100);
            $t->string('mata_kuliah', 100);
            $t->text('deskripsi')->nullable();
            $t->string('kode_undangan', 12)->unique();
            $t->unsignedInteger('id_pembuat');
            $t->dateTime('created_at')->nullable();
            $t->foreign('id_pembuat')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace');
    }
};
