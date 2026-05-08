@php
    $isAdmin = auth()->user()->hasRole('admin');
    $hasCompany = (bool) auth()->user()->company;
@endphp

<nav class="flex h-full flex-col">
    <div class="border-b border-white/10 px-6 py-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/15">
                <x-application-logo class="h-7 w-7 fill-current text-cyan-300" />
            </div>
            <div>
                <div class="text-lg font-bold tracking-tight text-white">{{ config('app.name', 'Shaghalni') }}</div>
                <div class="text-xs text-slate-400">Backoffice control panel</div>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-5">
        <div class="mb-6 rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs uppercase tracking-[0.25em] text-slate-400">Signed in</div>
            <div class="mt-2 text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-400">Role: {{ auth()->user()->roleName() }}</div>
        </div>

        <div class="space-y-6">
            <div>
                <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Overview</div>
                <ul class="space-y-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                </ul>
            </div>

            <div>
                <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Workflows</div>
                <ul class="space-y-1">
                    <x-nav-link :href="route('job-applications.index')" :active="request()->routeIs('job-applications.*')">
                        Job Applications
                    </x-nav-link>

                    <x-nav-link :href="route('job-vacancies.index')" :active="request()->routeIs('job-vacancies.*')">
                        Job Vacancies
                    </x-nav-link>

                    @if ($hasCompany)
                        <x-nav-link :href="route('my-company.show')" :active="request()->routeIs('my-company.*')">
                            My Company
                        </x-nav-link>

                        <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                            Departments
                        </x-nav-link>

                        <x-nav-link :href="route('company-employees.index')" :active="request()->routeIs('company-employees.*')">
                            Employees
                        </x-nav-link>
                    @endif
                </ul>
            </div>

            @if ($isAdmin)
                <div>
                    <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Administration</div>
                    <ul class="space-y-1">
                        <x-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')">
                            Companies
                        </x-nav-link>

                        <x-nav-link :href="route('job-categories.index')" :active="request()->routeIs('job-categories.*')">
                            Categories
                        </x-nav-link>

                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            Users
                        </x-nav-link>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="border-t border-white/10 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:border-rose-400/40 hover:bg-rose-500/10 hover:text-rose-100">
                Log Out
            </button>
        </form>
    </div>
</nav>
