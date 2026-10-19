<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct(private NotifikasiService $notif)
    {
    }

    private function rules(Workspace $workspace): array
    {
        return [
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'id_penanggung_jawab' => [
                'required',
                Rule::exists('workspace_member', 'id_user')->where('id_workspace', $workspace->id_workspace),
            ],
            'deadline' => 'required|date|after_or_equal:today',
        ];
    }

    private function pesanError(): array
    {
        return [
            'deadline.after_or_equal' => 'Deadline tidak boleh lebih awal dari hari ini.',
            'id_penanggung_jawab.exists' => 'Penanggung jawab harus anggota workspace.',
        ];
    }

    // FR-009, FR-011
    public function store(Request $request, Workspace $workspace)
    {
        $data = $request->validate($this->rules($workspace), $this->pesanError());

        $task = Task::create($data + [
            'id_workspace' => $workspace->id_workspace,
            'status' => 'todo',
            'dibuat_oleh' => Auth::id(),
        ]);

        $this->notif->kirim(
            User::find($task->id_penanggung_jawab),
            $task,
            'penugasan',
            sprintf('Anda ditugaskan: "%s" (%s), deadline %s.', $task->judul, $workspace->nama_workspace, $task->deadline->format('d/m/Y H:i'))
        );

        return back()->with('success', 'Tugas ditambahkan.');
    }

    // FR-010, FR-023
    public function update(Request $request, Workspace $workspace, Task $task)
    {
        abort_unless($task->id_workspace === $workspace->id_workspace, 404);

        $rules = $this->rules($workspace);
        if (Carbon::parse($request->input('deadline'))->equalTo($task->deadline)) {
            $rules['deadline'] = 'required|date'; // deadline tidak diubah, tidak perlu dicek lampau
        }

        $data = $request->validate($rules, $this->pesanError());

        $task->fill($data);
        $berubah = $task->isDirty('id_penanggung_jawab') || $task->isDirty('deadline');
        $task->save();

        if ($berubah) {
            $this->notif->kirim(
                User::find($task->id_penanggung_jawab),
                $task,
                'perubahan',
                sprintf('Tugas "%s" diperbarui. Deadline: %s.', $task->judul, $task->deadline->format('d/m/Y H:i'))
            );
        }

        return back()->with('success', 'Tugas diperbarui.');
    }

    public function destroy(Workspace $workspace, Task $task)
    {
        abort_unless($task->id_workspace === $workspace->id_workspace, 404);

        $task->delete();

        return back()->with('success', 'Tugas dihapus.');
    }
}
