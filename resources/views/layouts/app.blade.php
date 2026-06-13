<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Bahujan Collective') }}</title>
    <meta name="theme-color" content="#0f172a">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-[120px]"></div>
            <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-cyan-400/10 blur-[120px]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <header class="flex flex-col gap-4 rounded-2xl border border-slate-800/70 bg-slate-900/70 p-5 backdrop-blur lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-sky-500 to-cyan-400 text-lg font-semibold text-white shadow-lg shadow-indigo-500/30">
                        BC
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Bahujan Collective</p>
                        <p class="text-xl font-semibold text-white">Community Events</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <a class="rounded-full bg-slate-800/70 px-4 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70 {{ request()->routeIs('home') ? 'bg-slate-700/70' : '' }}" href="{{ route('home') }}">Home</a>
                    <a class="rounded-full bg-slate-800/70 px-4 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70 {{ request()->routeIs('about') ? 'bg-slate-700/70' : '' }}" href="{{ route('about') }}">About</a>
                    <a class="rounded-full bg-slate-800/70 px-4 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70 {{ request()->routeIs('submit-event') ? 'bg-slate-700/70' : '' }}" href="{{ route('submit-event') }}">Submit Events</a>
                    <a class="rounded-full bg-slate-800/70 px-4 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70 {{ request()->routeIs('contact') ? 'bg-slate-700/70' : '' }}" href="{{ route('contact') }}">Contact & Feedback</a>
                </div>
            </header>
        </div>
    </div>

    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @livewire('feedback-modal')

    @livewireScripts
</body>
</html>
