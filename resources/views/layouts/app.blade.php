<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TaskMate')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .btn{background:#4338ca;color:#fff;padding:.35rem .75rem;border-radius:.375rem;font-size:.875rem;cursor:pointer;display:inline-block;border:0}
        .btn-red{background:#dc2626}
        .inp{border:1px solid #d1d5db;border-radius:.375rem;padding:.35rem .5rem;width:100%;font-size:.875rem;background:#fff}
        .card{background:#fff;border-radius:.5rem;padding:1rem;box-shadow:0 1px 2px rgba(0,0,0,.08)}
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
@auth
    @php $belumDibaca = \App\Models\Notifikasi::where('id_user', auth()->id())->where('status_baca', false)->count(); @endphp
    <nav class="bg-indigo-700 text-white">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('workspace.index') }}" class="font-bold text-lg">TaskMate</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('workspace.index') }}">Dashboard</a>
                <a href="{{ route('notifikasi.index') }}">
                    🔔 Notifikasi
                    @if($belumDibaca > 0)
                        <span class="bg-red-500 rounded-full px-2 text-xs">{{ $belumDibaca }}</span>
                    @endif
                </a>
                <span>{{ auth()->user()->nama }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="underline">Keluar</button></form>
            </div>
        </div>
    </nav>
@endauth

<main class="max-w-6xl mx-auto p-4">
    @if(session('success'))
        <div class="mb-3 p-3 rounded bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-3 p-3 rounded bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-3 p-3 rounded bg-red-100 text-red-800 text-sm">
            <ul class="list-disc ml-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
