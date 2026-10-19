<?php

namespace App\Http\Controllers;

use App\Models\FileDokumen;
use App\Models\FileVersion;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RepositoryController extends Controller
{
    private const KUOTA_BYTE = 1073741824; // 1 GB per workspace

    public function index(Request $request, Workspace $workspace)
    {
        return view('repository.index', [
            'workspace' => $workspace,
            'peran' => $request->attributes->get('peran'),
            'files' => $workspace->files()->with(['versions.uploader', 'task'])->orderBy('nama_file')->get(),
            'tasks' => $workspace->tasks()->orderBy('judul')->get(),
            'terpakai' => $workspace->totalUkuran(),
            'kuota' => self::KUOTA_BYTE,
        ]);
    }

    // FR-016, FR-017, FR-018
    public function store(Request $request, Workspace $workspace)
    {
        $request->validate([
            'berkas' => 'required|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,png,jpg,jpeg',
            'id_task' => ['nullable', Rule::exists('task', 'id_task')->where('id_workspace', $workspace->id_workspace)],
            'catatan_revisi' => 'nullable|string|max:255',
        ], [
            'berkas.max' => 'Ukuran berkas maksimal 20 MB.',
            'berkas.mimes' => 'Format berkas tidak didukung.',
        ]);

        $berkas = $request->file('berkas');
        $nama = $berkas->getClientOriginalName();
        $ukuran = $berkas->getSize();

        if (mb_strlen($nama) > 150) {
            return back()->with('error', 'Nama berkas maksimal 150 karakter.');
        }

        if ($workspace->totalUkuran() + $ukuran > self::KUOTA_BYTE) {
            return back()->with('error', 'Kuota penyimpanan workspace (1 GB) terlampaui.');
        }

        $nomor = DB::transaction(function () use ($request, $workspace, $berkas, $nama, $ukuran) {
            $dok = FileDokumen::where('id_workspace', $workspace->id_workspace)
                ->where('nama_file', $nama)
                ->lockForUpdate()
                ->first();

            if (!$dok) {
                $dok = FileDokumen::create([
                    'id_workspace' => $workspace->id_workspace,
                    'id_task' => $request->input('id_task') ?: null,
                    'nama_file' => $nama,
                    'dibuat_oleh' => Auth::id(),
                ]);
            } elseif ($request->filled('id_task')) {
                $dok->update(['id_task' => $request->input('id_task')]);
            }

            $nomor = (int) $dok->versions()->max('nomor_versi') + 1;

            $path = $berkas->storeAs(
                "repository/{$workspace->id_workspace}/{$dok->id_file}",
                "v{$nomor}_".Str::random(8).'.'.$berkas->getClientOriginalExtension()
            );

            FileVersion::create([
                'id_file' => $dok->id_file,
                'nomor_versi' => $nomor,
                'path_file' => $path,
                'ukuran_byte' => $ukuran,
                'catatan_revisi' => $request->input('catatan_revisi'),
                'id_uploader' => Auth::id(),
                'waktu_unggah' => now(),
            ]);

            return $nomor;
        });

        return back()->with('success', "Berkas tersimpan sebagai versi v{$nomor}.");
    }

    // FR-019 (berkas tersimpan privat, unduhan hanya lewat aplikasi)
    public function unduh(Workspace $workspace, FileVersion $version)
    {
        $dok = $version->file;
        abort_unless($dok->id_workspace === $workspace->id_workspace, 404);

        $info = pathinfo($dok->nama_file);
        $nama = $info['filename'].'_v'.$version->nomor_versi.(isset($info['extension']) ? '.'.$info['extension'] : '');

        return Storage::download($version->path_file, $nama);
    }

    // FR-020
    public function destroy(Workspace $workspace, FileDokumen $file)
    {
        abort_unless($file->id_workspace === $workspace->id_workspace, 404);

        Storage::deleteDirectory("repository/{$workspace->id_workspace}/{$file->id_file}");
        $file->delete(); // seluruh versi terhapus lewat ON DELETE CASCADE

        return back()->with('success', 'Berkas beserta seluruh versinya dihapus.');
    }
}
