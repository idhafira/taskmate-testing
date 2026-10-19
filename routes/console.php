<?php

use Illuminate\Support\Facades\Schedule;

// Pengingat deadline setiap hari pukul 07.00 WITA.
Schedule::command('taskmate:kirim-pengingat')
    ->dailyAt('07:00')
    ->timezone('Asia/Makassar');
