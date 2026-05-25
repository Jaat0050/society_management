<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ $title ?? 'Guard' }} — Gate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    @include('partials.form-styles')
    <style>
        .guard-nav a.active { background: #4f46e5; color: white; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen">
    <header class="sticky top-0 z-20 bg-slate-950 border-b border-slate-800 px-4 py-3">
        <div class="flex items-center justify-between max-w-4xl mx-auto">
            <div>
                <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wider">Security Gate</p>
                <h1 class="font-bold text-lg">{{ $header ?? 'Dashboard' }}</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-red-400 border border-red-400/30 px-3 py-1.5 rounded-lg">Logout</button>
            </form>
        </div>
    </header>

    <nav class="guard-nav flex gap-2 overflow-x-auto px-4 py-3 max-w-4xl mx-auto text-sm">
        @foreach ($menuItems as $item)
            <a href="{{ route($item['route']) }}" wire:navigate
               class="shrink-0 px-3 py-2 rounded-lg {{ request()->routeIs($item['route'].'*') ? 'active' : 'bg-slate-800 text-slate-300' }}">
                {{ $item['icon'] }} {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <main class="px-4 py-5 max-w-4xl mx-auto pb-24">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-emerald-900/50 border border-emerald-600 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-xl bg-red-900/50 border border-red-600 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
