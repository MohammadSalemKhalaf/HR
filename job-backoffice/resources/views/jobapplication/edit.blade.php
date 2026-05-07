<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Operations</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit Applicant Status</h2>
                <p class="mt-2 text-sm text-slate-600">Review the candidate and update the application decision.</p>
            </div>

            <a href="{{ route('job-applications.show', $jobApplication->id) }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Applicant Name</p>
                    <p class="mt-2 font-medium text-slate-900">{{ $jobApplication->user?->name ?? '-' }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Job Vacancy</p>
                    <p class="mt-2 font-medium text-slate-900">{{ $jobApplication->jobvacancy?->title ?? '-' }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Company</p>
                    <p class="mt-2 font-medium text-slate-900">{{ $jobApplication->jobvacancy?->company?->name ?? '-' }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">AI Generated Score</p>
                    <p class="mt-2 font-medium text-slate-900">{{ $jobApplication->aiGeneratedScore ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-5">
                <p class="text-sm font-semibold text-slate-900">AI Generated Feedback</p>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $jobApplication->aiGeneratedFeedback ?? 'No feedback available' }}</p>
            </div>

            <form action="{{ route('job-applications.update', $jobApplication->id) }}" method="POST" class="mt-6 space-y-6 border-t border-slate-200 pt-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Application Status</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        <option value="pending" {{ $jobApplication->status == 'pending' ? 'selected' : '' }}>Pending - Under Review</option>
                        <option value="accepted" {{ $jobApplication->status == 'accepted' ? 'selected' : '' }}>Accepted - Qualified</option>
                        <option value="rejected" {{ $jobApplication->status == 'rejected' ? 'selected' : '' }}>Rejected - Disqualified</option>
                    </select>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('job-applications.show', $jobApplication->id) }}" class="rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">Update Applicant Status</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>