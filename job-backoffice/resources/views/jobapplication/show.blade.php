<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Application detail</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                    {{ $jobApplication->user?->name ?? '-' }}
                    <span class="text-slate-400">|</span>
                    {{ $jobApplication->jobVacancy?->title ?? '-' }}
                </h2>
                <p class="mt-2 text-sm text-slate-600">Inspect resume data, AI feedback, and application metadata.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('job-applications.edit', $jobApplication->id) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Edit</a>
                <a href="{{ route('job-applications.index') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Applicant</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $jobApplication->user?->name ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Company</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $jobApplication->jobVacancy?->company?->name ?? '-' }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Status</p>
                <p class="mt-2 font-semibold text-slate-900 capitalize">{{ $jobApplication->status }}</p>
            </div>
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Resume</p>
                <p class="mt-2 font-semibold text-slate-900">
                    @if($jobApplication->resume?->file_path)
                        <a href="{{ asset('storage/'.$jobApplication->resume->file_path) }}" target="_blank" class="text-cyan-700 hover:underline">Open file</a>
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
            <h3 class="text-lg font-semibold text-slate-900">Application Details</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2 text-sm text-slate-600">
                <div>
                    <p class="text-slate-500">Job Vacancy</p>
                    <p class="mt-1 font-medium text-slate-900">{{ $jobApplication->jobVacancy?->title ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-500">AI Score</p>
                    <p class="mt-1 font-medium text-slate-900">{{ $jobApplication->aiGeneratedScore ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div x-data="{ tab: 'resume' }" class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <div class="flex flex-wrap gap-2">
                    <button @click="tab='resume'" :class="tab==='resume' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'" class="rounded-full px-4 py-2 text-sm font-semibold transition">Resume</button>
                    <button @click="tab='feedback'" :class="tab==='feedback' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'" class="rounded-full px-4 py-2 text-sm font-semibold transition">AI Feedback</button>
                </div>
            </div>

            <div class="p-6">
                <div x-show="tab==='resume'" x-transition>
                    @if($jobApplication->resume)
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Summary</th>
                                        <th class="px-4 py-3">Skills</th>
                                        <th class="px-4 py-3">Experience</th>
                                        <th class="px-4 py-3">Education</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="px-4 py-4 align-top text-slate-600">{{ $jobApplication->resume->summary }}</td>
                                        <td class="px-4 py-4 align-top text-slate-600">{{ $jobApplication->resume->skills }}</td>
                                        <td class="px-4 py-4 align-top text-slate-600">{{ $jobApplication->resume->experience }}</td>
                                        <td class="px-4 py-4 align-top text-slate-600">{{ $jobApplication->resume->education }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No resume available.</div>
                    @endif
                </div>

                <div x-show="tab==='feedback'" x-transition>
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">AI Score</th>
                                    <th class="px-4 py-3">Feedback</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="px-4 py-4 align-top text-slate-600">{{ $jobApplication->aiGeneratedScore ?? '-' }}</td>
                                    <td class="px-4 py-4 whitespace-pre-line align-top text-slate-600">{{ $jobApplication->aiGeneratedFeedback ?? 'No feedback generated.' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>