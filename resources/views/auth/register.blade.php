@extends('layouts.app')
@section('title', 'Daftar | TaskMate')
@section('content')
<div class="max-w-sm mx-auto mt-12 card">
    <h1 class="text-xl font-bold mb-4">Daftar Akun</h1>
    <form method="POST" action="{{ route('register') }}" class="space-y-3">
        @csrf
        <div><label class="text-sm">Nama lengkap</label><input class="inp" name="nama" value="{{ old('nama') }}" required></div>
        <div><label class="text-sm">Email</label><input class="inp" type="email" name="email" value="{{ old('email') }}" required></div>
        <div><label class="text-sm">Kata sandi (min. 8 karakter)</label><input class="inp" type="password" name="password" required></div>
        <div><label class="text-sm">Ulangi kata sandi</label><input class="inp" type="password" name="password_confirmation" required></div>
        <button class="btn w-full">Daftar</button>
    </form>
    <p class="text-sm mt-3">Sudah punya akun? <a class="text-indigo-700 underline" href="{{ route('login') }}">Masuk</a></p>
    <a href="{{ route('google.redirect') }}"
    class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 hover:shadow">
        <svg class="h-5 w-5" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.9 2.4 30.4 0 24 0 14.6 0 6.5 5.4 2.6 13.2l7.9 6.1C12.4 13.6 17.7 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4.1 7.1-10.1 7.1-17.5z"/>
            <path fill="#FBBC05" d="M10.5 28.7a14.5 14.5 0 010-9.4l-7.9-6.1a24 24 0 000 21.6l7.9-6.1z"/>
            <path fill="#34A853" d="M24 48c6.5 0 11.9-2.1 15.9-5.8l-7.5-5.8c-2.1 1.4-4.9 2.3-8.4 2.3-6.3 0-11.6-4.1-13.5-9.8l-7.9 6.1C6.5 42.6 14.6 48 24 48z"/>
        </svg>
        Lanjutkan dengan Google
    </a>
</div>
@endsection
