<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::with('task.workspace')
            ->where('id_user', Auth::id())
            ->orderByDesc('waktu_kirim')
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    // Tandai sudah dibaca lalu buka workspace tugas terkait (UC-06 langkah 6)
    public function baca(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->id_user === Auth::id(), 403);

        $notifikasi->update(['status_baca' => true]);

        return redirect()->route('workspace.show', $notifikasi->task->id_workspace);
    }
}
