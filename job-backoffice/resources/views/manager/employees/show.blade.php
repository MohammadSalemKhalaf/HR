<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Management</p>
                <h2 class="text-3xl font-bold text-slate-900">{{ $employee->user?->name ?? 'Employee' }}</h2>
            </div>
            <a href="{{ route('manager.employees.index') }}" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">← Back to Employees</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Employee Information Card -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Employee Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Full Name</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->user?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Email Address</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->user?->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Department</p>
                    <p class="mt-2 font-semibold text-slate-900">
                        <a href="{{ route('manager.departments.show', $employee->department?->id) }}" class="text-cyan-600 hover:text-cyan-700">
                            {{ $employee->department?->name ?? '-' }}
                        </a>
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Employee ID</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->employee_number ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Employment Details -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Employment Details</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Job Title</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->job_title ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                    <p class="mt-2">
                        @if($employee->status === 'active')
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                ✓ Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                {{ ucfirst($employee->status) }}
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Salary (Read-only)</p>
                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $employee->salary !== null ? '$'.number_format((float) $employee->salary, 2) : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Hired Date</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->hired_at?->format('M d, Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid gap-4 md:grid-cols-2">
            <a href="{{ route('manager.attendance.index') }}" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
                <p class="text-xs uppercase tracking-wide text-slate-500">View Attendance</p>
                <p class="mt-2 text-sm font-semibold text-slate-900">Attendance Records</p>
            </a>

            <a href="{{ route('manager.leaves.index') }}" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
                <p class="text-xs uppercase tracking-wide text-slate-500">View Requests</p>
                <p class="mt-2 text-sm font-semibold text-slate-900">Leave Requests</p>
            </a>
        </div>
    </div>
</x-app-layout>
