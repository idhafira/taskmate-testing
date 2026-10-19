<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkspaceMember;
use Illuminate\Support\Facades\Mail;

class NotifikasiService
{
    public function kirim(User $user, Task $task, string $jenis, string $pesan, bool $cekDuplikat = false): ?Notifikasi
    {
        if ($cekDuplikat && Notifikasi::where('id_user', $user->id_user)
            ->where('id_task', $task->id_task)
            ->where('jenis', $jenis)
            ->exists()) {
            return null;
        }

        $notif = Notifikasi::create([
            'id_user' => $user->id_user,
            'id_task' => $task->id_task,
            'jenis' => $jenis,
            'pesan' => mb_substr($pesan, 0, 255),
            'status_baca' => false,
            'status_email' => 'menunggu',
            'waktu_kirim' => now(),
        ]);

        $notif->update([
            'status_email' => $this->kirimEmail($user->email, $pesan) ? 'terkirim' : 'gagal',
        ]);

        return $notif;
    }

    public function kirimKeLeader(Task $task, string $jenis, string $pesan, bool $cekDuplikat = false): void
    {
        $ids = WorkspaceMember::where('id_workspace', $task->id_workspace)
            ->where('peran', 'leader')
            ->pluck('id_user');

        foreach (User::whereIn('id_user', $ids)->get() as $leader) {
            $this->kirim($leader, $task, $jenis, $pesan, $cekDuplikat);
        }
    }

    // Percobaan kirim email maksimal 3 kali (UC-06, skenario 3a).
    private function kirimEmail(string $to, string $pesan): bool
    {
        for ($i = 1; $i <= 3; $i++) {
            try {
                Mail::raw($pesan, fn ($m) => $m->to($to)->subject('TaskMate: Notifikasi'));
                return true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return false;
    }
}
