@extends('layouts.app')
@section('title', 'Dashboard | TaskMate')
@section('content')
<h1 class="text-2xl font-bold mb-4">Workspace Saya</h1>

<div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="card md:col-span-2">
        <h2 class="font-semibold mb-2">Buat Workspace (menjadi Project Leader)</h2>
        <form method="POST" action="{{ route('workspace.store') }}" class="grid gap-2">
            @csrf
            <input class="inp" name="nama_workspace" placeholder="Nama workspace" required>
            <input class="inp" name="mata_kuliah" placeholder="Mata kuliah / judul tugas" required>
            <textarea class="inp" name="deskripsi" placeholder="Deskripsi (opsional)"></textarea>
            <div><button class="btn">Simpan</button></div>
        </form>
    </div>
    <div class="card">
        <h2 class="font-semibold mb-2">Gabung dengan Kode Undangan</h2>
        <form method="POST" action="{{ route('workspace.join') }}" class="grid gap-2">
            @csrf
            <input class="inp uppercase" name="kode_undangan" placeholder="Kode undangan" required>
            <div><button class="btn">Gabung</button></div>
        </form>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-4">
    @forelse($keanggotaan as $k)
        <a href="{{ route('workspace.show', $k->workspace) }}" class="card hover:shadow-md">
            <div class="flex justify-between items-start">
                <h3 class="font-semibold">{{ $k->workspace->nama_workspace }}</h3>
                <span class="text-xs px-2 py-1 rounded {{ $k->peran === 'leader' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200' }}">
                    {{ $k->peran === 'leader' ? 'Project Leader' : 'Member' }}
                </span>
            </div>
            <p class="text-sm text-gray-600">{{ $k->workspace->mata_kuliah }}</p>
        </a>
    @empty
        <p class="text-sm text-gray-600">Belum ada workspace. Buat baru atau gabung dengan kode undangan.</p>
    @endforelse
</div>
@endsection
