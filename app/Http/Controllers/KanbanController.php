<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatusLog;
use App\Models\Workspace;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KanbanController extends Controller
{
    private const URUTAN = ['todo', 'in_progress', 'review', 'done'];

    public function __construct(private NotifikasiService $notif)
    {
    }

    // FR-012, FR-015
    public function show(Request $request, Workspace $workspace)
    {
        $query = $workspace->tasks()->with('penanggungJawab');

        if ($request->filled('penanggung_jawab')) {
            $query->where('id_penanggung_jawab', $request->penanggung_jawab);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('kanban.show', [
            'workspace' => $workspace,
            'peran' => $request->attributes->get('peran'),
            'tasks' => $query->orderBy('deadline')->get()->groupBy('status'),
            'members' => $workspace->members()->with('user')->get(),
            'statusList' => Task::STATUS,
        ]);
    }

    // FR-013, FR-014
    public function updateStatus(Request $request, Workspace $workspace, Task $task)
    {
        abort_unless($task->id_workspace === $workspace->id_workspace, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in(self::URUTAN)],
            'catatan' => 'nullable|string|max:255',
        ]);

        $peran = $request->attributes->get('peran');
        $lama = $task->status;
        $baru = $data['status'];

        if ($lama === $baru) {
            return back();
        }

        if ($peran !== 'leader') {
            if ((int) $task->id_penanggung_jawab !== (int) Auth::id()) {
                return back()->with('error', 'Anda hanya dapat mengubah tugas milik Anda.');
            }

            $maju = array_search($baru, self::URUTAN) === array_search($lama, self::URUTAN) + 1;

            if ($baru === 'done' || !$maju) {
                return back()->with('error', 'Member hanya dapat memindahkan tugas ke status berikutnya sampai Review. Status Done ditetapkan Project Leader.');
            }
        }

        DB::transaction(function () use ($task, $lama, $baru, $data) {
            $task->update(['status' => $baru]);

            TaskStatusLog::create([
                'id_task' => $task->id_task,
                'id_user' => Auth::id(),
                'status_lama' => $lama,
                'status_baru' => $baru,
                'catatan' => $data['catatan'] ?? null,
                'waktu' => now(),
            ]);
        });

        if ($baru === 'review') {
            $this->notif->kirimKeLeader(
                $task,
                'review',
                sprintf('Tugas "%s" menunggu peninjauan Anda (Review).', $task->judul)
            );
        }

        return back()->with('success', 'Status tugas diperbarui.');
    }
}
