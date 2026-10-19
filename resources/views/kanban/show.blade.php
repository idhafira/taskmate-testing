@extends('layouts.app')
@section('title', $workspace->nama_workspace.' | TaskMate')
@section('content')
@php
    $urut = array_keys($statusList);
    $warnaKolom = ['todo' => 'bg-gray-200', 'in_progress' => 'bg-blue-100', 'review' => 'bg-yellow-100', 'done' => 'bg-green-100'];
@endphp

<div class="flex flex-wrap items-start justify-between gap-3 mb-4">
    <div>
        <h1 class="text-2xl font-bold">{{ $workspace->nama_workspace }}</h1>
        <p class="text-sm text-gray-600">{{ $workspace->mata_kuliah }} &middot; Peran Anda:
            <span class="font-semibold">{{ $peran === 'leader' ? 'Project Leader' : 'Member' }}</span></p>
        @if($workspace->deskripsi)<p class="text-sm mt-1">{{ $workspace->deskripsi }}</p>@endif
        @if($peran === 'leader')
            <p class="text-sm mt-1">Kode undangan:
                <code class="bg-white px-2 py-1 rounded border font-mono">{{ $workspace->kode_undangan }}</code></p>
        @endif
    </div>
    <div class="flex gap-2">
        <a class="btn" href="{{ route('workspace.show', $workspace) }}">Kanban</a>
        <a class="btn" href="{{ route('repository.index', $workspace) }}">Repository</a>
    </div>
</div>

<form method="GET" class="card mb-4 flex flex-wrap gap-2 items-end">
    <div><label class="text-xs">Penanggung jawab</label>
        <select name="penanggung_jawab" class="inp">
            <option value="">Semua</option>
            @foreach($members as $m)
                <option value="{{ $m->id_user }}" @selected(request('penanggung_jawab') == $m->id_user)>{{ $m->user->nama }}</option>
            @endforeach
        </select></div>
    <div><label class="text-xs">Status</label>
        <select name="status" class="inp">
            <option value="">Semua</option>
            @foreach($statusList as $k => $v)
                <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
            @endforeach
        </select></div>
    <button class="btn">Filter</button>
</form>

@if($peran === 'leader')
<details class="card mb-4">
    <summary class="cursor-pointer font-semibold">+ Tambah Tugas</summary>
    <form method="POST" action="{{ route('task.store', $workspace) }}" class="grid md:grid-cols-2 gap-2 mt-3">
        @csrf
        <input class="inp" name="judul" placeholder="Judul tugas" required>
        <select class="inp" name="id_penanggung_jawab" required>
            <option value="">Pilih penanggung jawab</option>
            @foreach($members as $m)<option value="{{ $m->id_user }}">{{ $m->user->nama }}</option>@endforeach
        </select>
        <textarea class="inp md:col-span-2" name="deskripsi" placeholder="Deskripsi"></textarea>
        <input class="inp" type="datetime-local" name="deadline" required>
        <div><button class="btn">Simpan Tugas</button></div>
    </form>
</details>
@endif

<div class="grid md:grid-cols-4 gap-3">
@foreach($statusList as $kodeStatus => $labelStatus)
    <div class="rounded-lg p-2 {{ $warnaKolom[$kodeStatus] }}">
        <h2 class="font-semibold text-sm mb-2">{{ $labelStatus }} ({{ ($tasks[$kodeStatus] ?? collect())->count() }})</h2>
        <div class="space-y-2">
        @foreach($tasks[$kodeStatus] ?? [] as $t)
            @php
                $sisa = (int) now()->startOfDay()->diffInDays($t->deadline->copy()->startOfDay(), false);
                $warna = $t->status === 'done' ? 'border-green-500' : ($sisa <= 0 ? 'border-red-500 bg-red-50' : ($sisa <= 3 ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300'));
                $milik = (int) $t->id_penanggung_jawab === auth()->id();
                $idx = array_search($t->status, $urut);
            @endphp
            <div class="bg-white border-l-4 {{ $warna }} rounded p-2 text-sm">
                <div class="font-semibold">{{ $t->judul }}</div>
                @if($t->deskripsi)<div class="text-xs text-gray-600">{{ $t->deskripsi }}</div>@endif
                <div class="text-xs mt-1">👤 {{ $t->penanggungJawab->nama }}</div>
                <div class="text-xs">⏰ {{ $t->deadline->format('d/m/Y H:i') }}
                    @if($t->terlambat)<span class="text-red-600 font-semibold">(Terlambat)</span>@endif</div>

                @if($peran === 'leader')
                    <form method="POST" action="{{ route('task.status', [$workspace, $t]) }}" class="mt-2 space-y-1">
                        @csrf @method('PATCH')
                        <select name="status" class="inp">
                            @foreach($statusList as $k => $v)<option value="{{ $k }}" @selected($t->status === $k)>{{ $v }}</option>@endforeach
                        </select>
                        <input name="catatan" class="inp" placeholder="Catatan (opsional)">
                        <button class="btn">Pindah</button>
                    </form>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-xs text-indigo-700">Ubah / hapus</summary>
                        <form method="POST" action="{{ route('task.update', [$workspace, $t]) }}" class="space-y-1 mt-1">
                            @csrf @method('PUT')
                            <input class="inp" name="judul" value="{{ $t->judul }}" required>
                            <textarea class="inp" name="deskripsi">{{ $t->deskripsi }}</textarea>
                            <select class="inp" name="id_penanggung_jawab">
                                @foreach($members as $m)<option value="{{ $m->id_user }}" @selected($t->id_penanggung_jawab == $m->id_user)>{{ $m->user->nama }}</option>@endforeach
                            </select>
                            <input class="inp" type="datetime-local" name="deadline" value="{{ $t->deadline->format('Y-m-d\TH:i') }}" required>
                            <button class="btn">Simpan</button>
                        </form>
                        <form method="POST" action="{{ route('task.destroy', [$workspace, $t]) }}" class="mt-1"
                              onsubmit="return confirm('Hapus tugas ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-red">Hapus</button>
                        </form>
                    </details>
                @elseif($milik && $idx < 2)
                    <form method="POST" action="{{ route('task.status', [$workspace, $t]) }}" class="mt-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $urut[$idx + 1] }}">
                        <button class="btn">Lanjut ke {{ $statusList[$urut[$idx + 1]] }}</button>
                    </form>
                @endif
            </div>
        @endforeach
        </div>
    </div>
@endforeach
</div>

<div class="grid md:grid-cols-2 gap-4 mt-6">
    <div class="card">
        <h2 class="font-semibold mb-2">Anggota</h2>
        <ul class="text-sm divide-y">
            @foreach($members as $m)
                <li class="py-1 flex justify-between items-center">
                    <span>{{ $m->user->nama }} <span class="text-xs text-gray-500">({{ $m->peran === 'leader' ? 'Project Leader' : 'Member' }})</span></span>
                    @if($peran === 'leader' && $m->peran !== 'leader')
                        <form method="POST" action="{{ route('workspace.anggota.destroy', [$workspace, $m]) }}"
                              onsubmit="return confirm('Keluarkan anggota ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs underline">Keluarkan</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    @if($peran === 'leader')
    <div class="card">
        <h2 class="font-semibold mb-2">Pengaturan Workspace</h2>
        <form method="POST" action="{{ route('workspace.update', $workspace) }}" class="grid gap-2">
            @csrf @method('PUT')
            <input class="inp" name="nama_workspace" value="{{ $workspace->nama_workspace }}" required>
            <input class="inp" name="mata_kuliah" value="{{ $workspace->mata_kuliah }}" required>
            <textarea class="inp" name="deskripsi">{{ $workspace->deskripsi }}</textarea>
            <div><button class="btn">Simpan</button></div>
        </form>
        <form method="POST" action="{{ route('workspace.destroy', $workspace) }}" class="mt-3"
              onsubmit="return confirm('Hapus workspace beserta seluruh tugas dan berkas?')">
            @csrf @method('DELETE')
            <button class="btn btn-red">Hapus Workspace</button>
        </form>
    </div>
    @endif
</div>
@endsection
