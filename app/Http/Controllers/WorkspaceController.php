<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WorkspaceController extends Controller
{
    // FR-008
    public function index()
    {
        $keanggotaan = WorkspaceMember::with('workspace')
            ->where('id_user', Auth::id())
            ->orderByDesc('tanggal_bergabung')
            ->get();

        return view('workspace.index', compact('keanggotaan'));
    }

    // FR-004, FR-005
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_workspace' => 'required|string|max:100',
            'mata_kuliah' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $workspace = DB::transaction(function () use ($data) {
            $ws = Workspace::create($data + [
                'kode_undangan' => Workspace::buatKode(),
                'id_pembuat' => Auth::id(),
            ]);

            WorkspaceMember::create([
                'id_workspace' => $ws->id_workspace,
                'id_user' => Auth::id(),
                'peran' => 'leader',
                'tanggal_bergabung' => now(),
            ]);

            return $ws;
        });

        return redirect()->route('workspace.show', $workspace)->with('success', 'Workspace berhasil dibuat.');
    }

    // FR-006
    public function join(Request $request)
    {
        $request->validate(['kode_undangan' => 'required|string|max:12']);

        $workspace = Workspace::where('kode_undangan', strtoupper(trim($request->kode_undangan)))->first();

        if (!$workspace) {
            return back()->with('error', 'Kode undangan tidak valid.');
        }

        $sudah = WorkspaceMember::where('id_workspace', $workspace->id_workspace)
            ->where('id_user', Auth::id())
            ->exists();

        if ($sudah) {
            return back()->with('error', 'Anda sudah menjadi anggota workspace ini.');
        }

        WorkspaceMember::create([
            'id_workspace' => $workspace->id_workspace,
            'id_user' => Auth::id(),
            'peran' => 'member',
            'tanggal_bergabung' => now(),
        ]);

        return redirect()->route('workspace.show', $workspace)->with('success', 'Berhasil bergabung ke workspace.');
    }

    // FR-007
    public function update(Request $request, Workspace $workspace)
    {
        $data = $request->validate([
            'nama_workspace' => 'required|string|max:100',
            'mata_kuliah' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $workspace->update($data);

        return back()->with('success', 'Workspace diperbarui.');
    }

    public function destroy(Workspace $workspace)
    {
        Storage::deleteDirectory('repository/'.$workspace->id_workspace);
        $workspace->delete(); // tugas, anggota, dan berkas terhapus lewat ON DELETE CASCADE

        return redirect()->route('workspace.index')->with('success', 'Workspace dihapus.');
    }

    public function removeMember(Workspace $workspace, WorkspaceMember $member)
    {
        abort_unless($member->id_workspace === $workspace->id_workspace, 404);

        if ($member->peran === 'leader') {
            return back()->with('error', 'Project Leader tidak dapat dikeluarkan.');
        }

        $punyaTugas = $workspace->tasks()->where('id_penanggung_jawab', $member->id_user)->exists();

        if ($punyaTugas) {
            return back()->with('error', 'Anggota masih memiliki tugas. Pindahkan penanggung jawab tugasnya terlebih dahulu.');
        }

        $member->delete();

        return back()->with('success', 'Anggota dikeluarkan.');
    }
}
