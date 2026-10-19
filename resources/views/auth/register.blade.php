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
</div>
@endsection
