@extends('layouts.app')
@section('title', 'Repository | '.$workspace->nama_workspace)
@section('content')
<div class="flex flex-wrap items-start justify-between gap-3 mb-4">
    <div>
        <h1 class="text-2xl font-bold">Repository: {{ $workspace->nama_workspace }}</h1>
        <p class="text-sm text-gray-600">Terpakai {{ number_format($terpakai / 1048576, 2) }} MB dari {{ number_format($kuota / 1048576) }} MB</p>
    </div>
    <div class="flex gap-2">
        <a class="btn" href="{{ route('workspace.show', $workspace) }}">Kanban</a>
        <a class="btn" href="{{ route('repository.index', $workspace) }}">Repository</a>
    </div>
</div>

<div class="card mb-4">
    <h2 class="font-semibold mb-2">Unggah Draf</h2>
    <p class="text-xs text-gray-600 mb-2">Format: PDF, DOC/DOCX, PPT/PPTX, XLS/XLSX, TXT, ZIP, PNG, JPG. Maksimal 20 MB. Nama berkas yang sama akan disimpan sebagai versi baru.</p>
    <form method="POST" action="{{ route('repository.store', $workspace) }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-2">
        @csrf
        <input class="inp" type="file" name="berkas" required>
        <select class="inp" name="id_task">
            <option value="">Tidak dikaitkan ke tugas</option>
            @foreach($tasks as $t)<option value="{{ $t->id_task }}">{{ $t->judul }}</option>@endforeach
        </select>
        <input class="inp md:col-span-2" name="catatan_revisi" placeholder="Catatan revisi (opsional)">
        <div><button class="btn">Unggah</button></div>
    </form>
</div>

<div class="space-y-3">
@forelse($files as $f)
    <div class="card">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-semibold">{{ $f->nama_file }}</h3>
                @if($f->task)<p class="text-xs text-gray-600">Tugas terkait: {{ $f->task->judul }}</p>@endif
            </div>
            @if($peran === 'leader')
                <form method="POST" action="{{ route('repository.destroy', [$workspace, $f]) }}"
                      onsubmit="return confirm('Hapus berkas beserta seluruh versinya?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-red">Hapus</button>
                </form>
            @endif
        </div>
        <table class="w-full text-sm mt-2">
            <thead><tr class="text-left text-xs text-gray-500">
                <th>Versi</th><th>Pengunggah</th><th>Waktu</th><th>Ukuran</th><th>Catatan</th><th></th></tr></thead>
            <tbody>
            @foreach($f->versions as $v)
                <tr class="border-t">
                    <td>v{{ $v->nomor_versi }}</td>
                    <td>{{ $v->uploader->nama }}</td>
                    <td>{{ $v->waktu_unggah->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($v->ukuran_byte / 1024, 1) }} KB</td>
                    <td>{{ $v->catatan_revisi }}</td>
                    <td><a class="text-indigo-700 underline" href="{{ route('repository.unduh', [$workspace, $v]) }}">Unduh</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@empty
    <p class="text-sm text-gray-600">Belum ada berkas.</p>
@endforelse
</div>
@endsection
