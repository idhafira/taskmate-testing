@extends('layouts.app')
@section('title', 'Notifikasi | TaskMate')
@section('content')
<h1 class="text-2xl font-bold mb-4">Notifikasi</h1>
<div class="space-y-2">
@forelse($notifikasi as $n)
    <form method="POST" action="{{ route('notifikasi.baca', $n) }}">
        @csrf
        <button class="card w-full text-left {{ $n->status_baca ? 'opacity-70' : 'border-l-4 border-indigo-600' }}">
            <div class="flex justify-between text-xs text-gray-500">
                <span class="uppercase">{{ str_replace('_', '-', $n->jenis) }} &middot; {{ $n->task->workspace->nama_workspace }}</span>
                <span>{{ $n->waktu_kirim->format('d/m/Y H:i') }} &middot; email: {{ $n->status_email }}</span>
            </div>
            <div class="{{ $n->status_baca ? '' : 'font-semibold' }}">{{ $n->pesan }}</div>
        </button>
    </form>
@empty
    <p class="text-sm text-gray-600">Belum ada notifikasi.</p>
@endforelse
</div>
<div class="mt-4">{{ $notifikasi->links() }}</div>
@endsection
