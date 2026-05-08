<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Backoffice overview</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Dashboard</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">
                    Track hiring activity, vacancy growth, and application volume from one place.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('companies.index') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Manage Companies
                    </a>
                @else
                    <a href="{{ route('my-company.show') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        View Company
                    </a>
                @endif

                <a href="{{ route('job-vacancies.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                    Open Vacancies
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm font-medium text-slate-500">Active Users</p>
                <div class="mt-3 text-4xl font-bold tracking-tight text-slate-900">{{ $analytics['activeUsers'] ?? 0 }}</div>
                <p class="mt-2 text-sm text-slate-500">Job seekers active in the last 30 days</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm font-medium text-slate-500">Total Jobs</p>
                <div class="mt-3 text-4xl font-bold tracking-tight text-slate-900">{{ $analytics['totalJobs'] ?? 0 }}</div>
                <p class="mt-2 text-sm text-slate-500">Current active vacancies</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm font-medium text-slate-500">Total Applications</p>
                <div class="mt-3 text-4xl font-bold tracking-tight text-slate-900">{{ $analytics['totalApplications'] ?? 0 }}</div>
                <p class="mt-2 text-sm text-slate-500">All applications in the system</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Most Applied Jobs</h3>
                    <p class="text-sm text-slate-500">Vacancies with the strongest candidate traction</p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Job Title</th>
                                <th class="px-4 py-3">Company</th>
                                <th class="px-4 py-3 text-right">Applications</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($mostAppliedJobs as $job)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 font-medium text-slate-900">{{ $job->title }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $job->company?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ $job->totalCount }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Conversion Rates</h3>
                    <p class="text-sm text-slate-500">Vacancy engagement by view-to-apply ratio</p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Job Title</th>
                                <th class="px-4 py-3 text-right">Views</th>
                                <th class="px-4 py-3 text-right">Applications</th>
                                <th class="px-4 py-3 text-right">Conversion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($conversionRates as $job)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 font-medium text-slate-900">{{ $job->title }}</td>
                                    <td class="px-4 py-4 text-right text-slate-600">{{ $job->viewCount }}</td>
                                    <td class="px-4 py-4 text-right text-slate-600">{{ $job->totalCount }}</td>
                                    <td class="px-4 py-4 text-right font-semibold text-cyan-700">{{ $job->conversionRate }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
