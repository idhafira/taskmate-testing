<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_member', function (Blueprint $t) {
            $t->increments('id_member');
            $t->unsignedInteger('id_workspace');
            $t->unsignedInteger('id_user');
            $t->enum('peran', ['leader', 'member'])->default('member');
            $t->dateTime('tanggal_bergabung')->nullable();
            $t->unique(['id_workspace', 'id_user']);
            $t->foreign('id_workspace')->references('id_workspace')->on('workspace')->cascadeOnDelete();
            $t->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_member');
    }
};
