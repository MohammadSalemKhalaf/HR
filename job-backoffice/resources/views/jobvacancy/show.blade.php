<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Vacancy detail</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $jobVacancy->title }}</h2>
                <p class="mt-2 text-sm text-slate-600">Review the vacancy, attached company, and candidate applications.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('job-vacancies.edit', $jobVacancy->id) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Edit</a>
                <a href="{{ route('job-vacancies.index') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Company</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $jobVacancy->company?->name ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Category</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $jobVacancy->jobcategory?->name ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Type</p>
                <p class="mt-2 font-semibold text-slate-900">{{ ucfirst($jobVacancy->type) ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Salary</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $jobVacancy->salary ? '$'.number_format($jobVacancy->salary, 2) : '-' }}</p>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
            <h3 class="text-lg font-semibold text-slate-900">Description</h3>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $jobVacancy->description ?? '-' }}</p>
        </div>

        <div x-data="{ tab: 'applications' }" class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <button @click="tab='applications'" :class="tab==='applications' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'" class="rounded-full px-4 py-2 text-sm font-semibold transition">
                    Applications ({{ $jobVacancy->jobApplications->count() }})
                </button>
            </div>

            <div class="p-6">
                <div x-show="tab==='applications'" x-transition>
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Applicant</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($jobVacancy->jobApplications as $app)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-4 font-medium text-slate-900">{{ $app->user->name ?? '-' }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $app->status === 'accepted' ? 'bg-emerald-100 text-emerald-700' : ($app->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ ucfirst($app->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right text-slate-600">{{ $app->aiGeneratedScore ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-10 text-center text-slate-500">No applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>