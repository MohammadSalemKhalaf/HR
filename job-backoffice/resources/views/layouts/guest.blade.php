<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.18),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.14),_transparent_28%),linear-gradient(180deg,_#f8fafc_0%,_#e2e8f0_100%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="hidden rounded-[2rem] border border-white/50 bg-slate-950/95 p-8 text-white shadow-2xl shadow-slate-950/20 lg:block">
                        <div class="flex items-center gap-3">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/15">
                                <x-application-logo class="h-8 w-8 fill-current text-cyan-300" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold tracking-tight">{{ config('app.name', 'Shaghalni') }}</div>
                                <div class="text-sm text-slate-400">Backoffice & recruitment operations</div>
                            </div>
                        </div>

                        <div class="mt-10 space-y-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-sm font-semibold text-cyan-200">Admin ready</div>
                                <p class="mt-1 text-sm text-slate-300">Manage companies, vacancies, categories, users, and applications from one consistent panel.</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="text-sm font-semibold text-emerald-200">Fast access</div>
                                <p class="mt-1 text-sm text-slate-300">A clearer layout makes it easy to move between workflows without losing context.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center">
                        <div class="w-full max-w-md rounded-[2rem] border border-white/60 bg-white/85 p-6 shadow-2xl shadow-slate-950/10 backdrop-blur-xl sm:p-8">
                            <div class="mb-6 flex items-center gap-3 lg:hidden">
                                <x-application-logo class="h-10 w-10 fill-current text-slate-800" />
                                <div>
                                    <div class="text-lg font-bold tracking-tight">{{ config('app.name', 'Shaghalni') }}</div>
                                    <div class="text-sm text-slate-500">Backoffice & recruitment operations</div>
                                </div>
                            </div>

                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
