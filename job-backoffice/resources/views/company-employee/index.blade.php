<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Employees</h2>
                <p class="mt-2 text-sm text-slate-600">Manage and view all your company employees.</p>
            </div>

            <a href="{{ route('company-employees.create') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Add Employee
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Total Employees</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Active Employees</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['active'] }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-lg shadow-slate-950/5 backdrop-blur">
                <p class="text-sm text-slate-500">Managers</p>
                <p class="mt-2 text-3xl font-bold text-blue-700">{{ $stats['managers'] }}</p>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="overflow-x-auto rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-left">Department</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Salary</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $employee->user?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $employee->user?->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $employee->user?->hasRole('manager') ? 'Manager' : 'Employee' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $employee->department?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $employee->job_title ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $employee->salary !== null ? '$'.number_format((float) $employee->salary, 2) : '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $employee->status === 'active' ? 'bg-emerald-100 text-emerald-700' : ($employee->status === 'probation' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('company-employees.show', $employee->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">View</a>
                                    <a href="{{ route('company-employees.edit', $employee->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">No employees found. Create one to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
