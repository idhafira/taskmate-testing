<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/workspaces');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // UC-02 Workspace
    Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspace.index');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspace.store');
    Route::post('/workspaces/join', [WorkspaceController::class, 'join'])->name('workspace.join');

    // UC-06 Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    Route::prefix('/workspaces/{workspace}')->group(function () {
        // Anggota workspace (Leader dan Member)
        Route::middleware('ws')->group(function () {
            Route::get('/', [KanbanController::class, 'show'])->name('workspace.show');
            Route::patch('/tasks/{task}/status', [KanbanController::class, 'updateStatus'])->name('task.status');

            Route::get('/repository', [RepositoryController::class, 'index'])->name('repository.index');
            Route::post('/repository', [RepositoryController::class, 'store'])->name('repository.store');
            Route::get('/repository/versi/{version}/unduh', [RepositoryController::class, 'unduh'])->name('repository.unduh');
        });

        // Khusus Project Leader
        Route::middleware('ws:leader')->group(function () {
            Route::put('/', [WorkspaceController::class, 'update'])->name('workspace.update');
            Route::delete('/', [WorkspaceController::class, 'destroy'])->name('workspace.destroy');
            Route::delete('/anggota/{member}', [WorkspaceController::class, 'removeMember'])->name('workspace.anggota.destroy');

            Route::post('/tasks', [TaskController::class, 'store'])->name('task.store');
            Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('task.update');
            Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('task.destroy');

            Route::delete('/repository/{file}', [RepositoryController::class, 'destroy'])->name('repository.destroy');
        });
    });
});
