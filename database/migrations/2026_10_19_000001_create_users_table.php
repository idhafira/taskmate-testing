<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->increments('id_user');
            $t->string('nama', 100);
            $t->string('email', 100)->unique();
            $t->string('password', 255);
            $t->unsignedInteger('gagal_login')->default(0);
            $t->dateTime('terkunci_sampai')->nullable();
            $t->dateTime('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
