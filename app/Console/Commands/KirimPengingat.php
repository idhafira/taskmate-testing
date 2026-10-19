<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KirimPengingat extends Command
{
    protected $signature = 'taskmate:kirim-pengingat';
    protected $description = 'Kirim pengingat deadline H-3, H-1, hari-H, dan tugas terlambat';

    public function handle(NotifikasiService $notif): int
    {
        $tasks = Task::with(['penanggungJawab', 'workspace'])
            ->where('status', '!=', 'done')
            ->get();

        $jumlah = 0;

        foreach ($tasks as $task) {
            $hari = (int) Carbon::today()->diffInDays($task->deadline->copy()->startOfDay(), false);

            $jenis = match (true) {
                $hari === 3 => 'h3',
                $hari === 1 => 'h1',
                $hari === 0 => 'hari_h',
                $hari < 0 => 'terlambat',
                default => null,
            };

            if ($jenis === null) {
                continue;
            }

            $keterangan = match ($jenis) {
                'h3' => 'jatuh tempo 3 hari lagi',
                'h1' => 'jatuh tempo besok',
                'hari_h' => 'jatuh tempo hari ini',
                'terlambat' => 'sudah melewati deadline',
            };

            $pesan = sprintf(
                'Tugas "%s" (%s) %s, deadline %s.',
                $task->judul,
                $task->workspace->nama_workspace,
                $keterangan,
                $task->deadline->format('d/m/Y H:i')
            );

            if ($notif->kirim($task->penanggungJawab, $task, $jenis, $pesan, true)) {
                $jumlah++;
            }

            if ($jenis === 'terlambat') {
                $notif->kirimKeLeader($task, $jenis, $pesan, true);
            }
        }

        $this->info("Pengingat dibuat: {$jumlah}");

        return self::SUCCESS;
    }
}
