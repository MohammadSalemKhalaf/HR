<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Backoffice overview</p>
            <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ __('Dashboard') }}</h2>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-8 shadow-lg shadow-slate-950/5 backdrop-blur">
            <h3 class="text-lg font-semibold text-slate-900">Welcome back</h3>
            <p class="mt-2 text-sm text-slate-600">
                Use the sidebar to move through companies, vacancies, applications, categories, and users.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('job-vacancies.index') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    View Vacancies
                </a>

                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('companies.index') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Manage Companies
                    </a>
                @endif
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/70 bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-white shadow-xl shadow-slate-950/10">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Status</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-2xl font-bold">Live</div>
                    <div class="mt-1 text-sm text-slate-300">API ready</div>
                </div>
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-2xl font-bold">UI</div>
                    <div class="mt-1 text-sm text-slate-300">Unified blade shell</div>
                </div>
                <div class="rounded-2xl bg-white/5 p-4">
                    <div class="text-2xl font-bold">Flow</div>
                    <div class="mt-1 text-sm text-slate-300">Connected modules</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
