<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company profile</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $company->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">Overview of the company, vacancies, and applications.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ auth()->user()->hasRole('admin') ? route('companies.edit', $company->id) : route('my-company.edit') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Edit</a>

                @if(auth()->user()->hasRole('admin'))
                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Archive this company?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-full border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">Archive</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Address</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $company->address ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Industry</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $company->industry ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Owner</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $company->owner->name ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Website</p>
                <p class="mt-2 font-semibold text-slate-900">
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" class="text-cyan-700 hover:underline">Open website</a>
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        <div x-data="{ tab: 'jobs' }" class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <div class="flex flex-wrap gap-2">
                    <button @click="tab='jobs'" :class="tab==='jobs' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'" class="rounded-full px-4 py-2 text-sm font-semibold transition">
                        Jobs ({{ $company->jobVacancies->count() }})
                    </button>
                    <button @click="tab='applications'" :class="tab==='applications' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'" class="rounded-full px-4 py-2 text-sm font-semibold transition">
                        Applications ({{ $company->jobApplications->count() }})
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div x-show="tab==='jobs'" x-transition>
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Location</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($company->jobVacancies as $job)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-4 font-medium text-slate-900">{{ $job->title }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ ucfirst($job->type) }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $job->location }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('job-vacancies.show', $job->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">No jobs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="tab==='applications'" x-cloak x-transition>
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Applicant</th>
                                    <th class="px-4 py-3">Vacancy</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($company->jobApplications as $app)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-4 font-medium text-slate-900">{{ $app->user->name ?? '-' }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $app->jobVacancy->title ?? '-' }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $app->status === 'accepted' ? 'bg-emerald-100 text-emerald-700' : ($app->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                                {{ ucfirst($app->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right text-slate-600">{{ $app->aiGeneratedScore ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">No applications found.</td>
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
