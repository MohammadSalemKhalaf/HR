<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Operations</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Job Applications</h2>
                <p class="mt-2 text-sm text-slate-600">Review applications and manage archived records.</p>
            </div>

            @if(request()->boolean('archived'))
                <a href="{{ route('job-applications.index') }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Active Applications
                </a>
            @else
                <a href="{{ route('job-applications.index', ['archived' => 'true']) }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Archived Applications
                </a>
            @endif
        </div>
    </x-slot>

    <div class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <p class="text-sm font-medium text-slate-500">Applications List</p>
            <h3 class="text-lg font-semibold text-slate-900">Review and action records</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Applicant Name</th>
                        <th class="px-6 py-3 text-left">Position</th>
                        @if (auth()->user()->hasRole('admin'))
                            <th class="px-6 py-3 text-left">Company</th>
                        @endif
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($jobApplications as $application)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                <a href="{{ route('job-applications.show', $application->id) }}" class="hover:text-cyan-700 hover:underline">{{ $application->user?->name ?? 'N/A' }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $application->jobVacancy?->title ?? 'N/A' }}</td>
                            @if (auth()->user()->hasRole('admin'))
                                <td class="px-6 py-4 text-slate-600">{{ $application->jobVacancy?->company?->name ?? 'N/A' }}</td>
                            @endif
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $application->status === 'accepted' ? 'bg-emerald-100 text-emerald-700' : ($application->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if(request()->boolean('archived'))
                                        <form action="{{ route('job-applications.restore', $application->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700">Restore</button>
                                        </form>
                                    @else
                                        <a href="{{ route('job-applications.edit', $application->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Edit</a>
                                        <form action="{{ route('job-applications.destroy', $application->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-600 hover:text-rose-700">Archive</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('admin') ? 5 : 4 }}" class="px-6 py-12 text-center text-slate-500">No job applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $jobApplications->withQueryString()->links() }}
        </div>
    </div>

</x-app-layout>
