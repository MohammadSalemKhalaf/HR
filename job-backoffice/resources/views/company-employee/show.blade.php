<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Employees</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $employee->user?->name ?? 'Employee' }}</h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('company-employees.edit', $employee->id) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Edit</a>
                <form action="{{ route('company-employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Archive this employee?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                        Delete
                    </button>
                </form>
                <a href="{{ route('company-employees.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Employee Info Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Email</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $employee->user?->email ?? '-' }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Status</p>
                <p class="mt-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $employee->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Job Title</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $employee->job_title ?? '-' }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Department</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $employee->department?->name ?? '-' }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Salary</p>
                <p class="mt-2 font-semibold text-slate-900">
                    {{ $employee->salary !== null ? '$'.number_format((float) $employee->salary, 2) : '-' }}
                </p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Hire Date</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $employee->hired_at?->format('M d, Y') ?? '-' }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Manager</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $employee->manager?->user?->name ?? '-' }}</p>
            </div>
        </div>
    </div>

</x-app-layout>
