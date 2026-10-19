<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · TaskMate</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-violet-100 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-2xl text-white shadow-lg shadow-indigo-500/30">✓</div>
                <h1 class="text-2xl font-bold text-slate-900">Selamat datang di TaskMate</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola tugas Anda dengan lebih rapi</p>
            </div>

            <div class="rounded-3xl border border-white/60 bg-white/80 p-8 shadow-xl shadow-slate-200/60 backdrop-blur">

                <a href="{{ route('google.redirect') }}"
                   class="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 hover:shadow">
                    {{-- svg Google dari atas --}}
                    Lanjutkan dengan Google
                </a>

                <div class="my-6 flex items-center gap-3 text-xs text-slate-400">
                    <div class="h-px flex-1 bg-slate-200"></div>atau<div class="h-px flex-1 bg-slate-200"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                        <input type="password" name="password" required
                               class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    </div>
                    <button type="submit"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-700 active:scale-[.99]">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>