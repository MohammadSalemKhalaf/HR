<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.16),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(34,197,94,0.12),_transparent_28%),linear-gradient(180deg,_#f8fafc_0%,_#eef2ff_100%)]">
        <div class="mx-auto flex min-h-screen max-w-[1800px] flex-col lg:flex-row">
            <aside class="border-b border-slate-200 bg-slate-950/95 text-white shadow-2xl shadow-slate-950/10 lg:w-80 lg:border-b-0 lg:border-r lg:border-slate-800">
                @include('layouts.navigation')
            </aside>

            <div class="flex min-h-screen flex-1 flex-col">
                @isset($header)
                    <header class="border-b border-white/60 bg-white/70 backdrop-blur-xl">
                        <div class="mx-auto w-full max-w-[1400px] px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-6">
                        @if(session('success'))
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm shadow-emerald-100">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-sm shadow-rose-100">
                                {{ session('error') }}
                            </div>
                        @endif

                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>

</html>
