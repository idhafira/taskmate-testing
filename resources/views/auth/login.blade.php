@extends('layouts.app')
@section('title', 'Masuk | TaskMate')
@section('content')
<div class="max-w-sm mx-auto mt-12 card">
    <h1 class="text-xl font-bold mb-4">Masuk ke TaskMate</h1>
    <form method="POST" action="{{ route('login') }}" class="space-y-3">
        @csrf
        <div><label class="text-sm">Email</label><input class="inp" type="email" name="email" value="{{ old('email') }}" required></div>
        <div><label class="text-sm">Kata sandi</label><input class="inp" type="password" name="password" required></div>
        <button class="btn w-full">Masuk</button>
    </form>
    <p class="text-sm mt-3">Belum punya akun? <a class="text-indigo-700 underline" href="{{ route('register') }}">Daftar</a></p>
</div>
@endsection
