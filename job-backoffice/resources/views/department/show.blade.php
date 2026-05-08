<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Departments</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">{{ $department->name }}</h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('departments.edit', $department->id) }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Edit</a>
                <a href="{{ route('departments.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Department Info Card -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Department Code</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $department->code ?? '-' }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Employees</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $employees->count() }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Manager</p>
                <p class="mt-2 font-semibold text-slate-900">
                    @if($department->manager?->user)
                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                            {{ $department->manager->user->name }}
                        </span>
                    @else
                        <span class="text-slate-400">Unassigned</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
            <div class="border-b border-white/30 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Department Employees</h3>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Salary</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $emp->user?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $emp->user?->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $emp->user?->hasRole('manager') ? 'Manager' : 'Employee' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $emp->job_title ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $emp->salary !== null ? '$'.number_format((float) $emp->salary, 2) : '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $emp->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($emp->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">No employees in this department yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($employees->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
