<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Operations</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                    Job Vacancies
                    @if(request('archived') == 'true')
                        <span class="ml-2 text-sm font-medium text-slate-500">(Archived)</span>
                    @endif
                </h2>
                <p class="mt-2 text-sm text-slate-600">Manage open roles and their archived history.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if(request('archived') == 'true')
                    <a href="{{ route('job-vacancies.index') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Active Vacancies
                    </a>
                @else
                    <a href="{{ route('job-vacancies.index', ['archived' => 'true']) }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Archived Vacancies
                    </a>

                    <a href="{{ route('job-vacancies.create') }}" class="rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Add Vacancy
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <p class="text-sm font-medium text-slate-500">Vacancies List</p>
            <h3 class="text-lg font-semibold text-slate-900">Total {{ $jobVacancies->total() }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Title</th>
                        @if(auth()->user()->role == 'admin')
                            <th class="px-6 py-3 text-left">Company</th>
                        @endif
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-left">Location</th>
                        <th class="px-6 py-3 text-left">Type</th>
                        <th class="px-6 py-3 text-left">Salary</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse ($jobVacancies as $job)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                <a href="{{ route('job-vacancies.show', $job->id) }}" class="hover:text-cyan-700 hover:underline">{{ $job->title }}</a>
                            </td>
                            @if(auth()->user()->role == 'admin')
                                <td class="px-6 py-4 text-slate-600">{{ $job->company?->name ?? '-' }}</td>
                            @endif
                            <td class="px-6 py-4 text-slate-600">{{ $job->jobcategory?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $job->location }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ ucfirst($job->type) }}</td>
                            <td class="px-6 py-4 text-slate-600">${{ number_format($job->salary, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if(request('archived') == 'true')
                                        <form action="{{ route('job-vacancies.restore', $job->id) }}" method="POST" onsubmit="return confirm('Restore this vacancy?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700">Restore</button>
                                        </form>
                                    @else
                                        <a href="{{ route('job-vacancies.edit', $job->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Edit</a>
                                        <form action="{{ route('job-vacancies.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="{{ auth()->user()->role == 'admin' ? 7 : 6 }}" class="px-6 py-12 text-center text-slate-500">No vacancies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $jobVacancies->withQueryString()->links() }}
        </div>
    </div>

</x-app-layout>