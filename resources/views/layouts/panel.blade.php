<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    @include('partials.form-styles')
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="flex min-h-screen">
        <aside class="w-72 bg-slate-950 text-white flex flex-col shrink-0 shadow-2xl">
            <div class="relative overflow-hidden p-6 border-b border-white/10">
                <div class="absolute -top-10 -right-10 h-32 w-32 rounded-full bg-indigo-500/30 blur-2xl"></div>
                <div class="absolute -bottom-12 -left-10 h-32 w-32 rounded-full bg-cyan-400/20 blur-2xl"></div>
                <div class="relative">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center font-black shadow-lg">
                        SM
                    </div>
                    <p class="text-xs uppercase tracking-[0.25em] text-cyan-200 mt-4">{{ $panelLabel ?? 'Panel' }}</p>
                    <h1 class="font-bold text-xl mt-1">{{ config('app.name') }}</h1>
                </div>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @foreach ($menuItems as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate
                       class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition {{ request()->routeIs($item['route'].'*') ? 'bg-white text-slate-950 shadow-lg shadow-indigo-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <span class="grid h-8 w-8 place-items-center rounded-lg {{ request()->routeIs($item['route'].'*') ? 'bg-indigo-50' : 'bg-white/5 group-hover:bg-white/10' }}">{{ $item['icon'] ?? '•' }}</span>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="p-4 border-t border-white/10">
                <div class="rounded-2xl bg-white/5 p-4">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate mt-1">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-red-400/30 px-3 py-2 text-xs font-semibold text-red-300 hover:bg-red-500/10">Logout</button>
                </form>
            </div>
        </aside>
        <main class="flex-1 overflow-auto">
            <header class="sticky top-0 z-10 border-b border-white/70 bg-white/80 px-8 py-5 backdrop-blur">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-500">Management Console</p>
                        <h2 class="text-2xl font-bold text-slate-900">{{ $header ?? 'Dashboard' }}</h2>
                    </div>
                    <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-500 shadow-sm">
                        {{ now()->format('d M Y') }}
                    </div>
                </div>
            </header>
            <div class="p-8">
                @if (session('success'))
                    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">{{ session('error') }}</div>
                @endif
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts
    @stack('scripts')
    <div wire:loading.flex wire:target="save,delete,toggleBlock,edit,create,saveAmenity,publish,send,generateBulk,markPaid,updateStatus,updateBookingStatus,toggleActive,authenticate"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/20 backdrop-blur-sm">
        <div class="rounded-xl bg-white px-6 py-4 shadow-xl text-sm font-medium text-slate-700">Please wait...</div>
    </div>
</body>
</html>
