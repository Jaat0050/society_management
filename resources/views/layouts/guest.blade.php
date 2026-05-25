<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Login' }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    @include('partials.form-styles')
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    {{ $slot }}
    @livewireScripts
</body>
</html>
