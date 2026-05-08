<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Self Service</p>
                <h2 class="text-3xl font-bold text-slate-900">My Profile</h2>
            </div>
            <a href="{{ route('employee.profile.edit') }}" class="rounded-full bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-500 transition">
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Personal Information -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Personal Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div class="py-2">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Full Name</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $employee->user?->name ?? '-' }}</p>
                </div>
                <div class="py-2">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Email Address</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $employee->user?->email ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Employment Information (Read-only) -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Employment Information</h3>
                <p class="text-xs text-slate-500 mt-1">These fields cannot be edited. Contact your manager if changes are needed.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div class="py-2 rounded-lg bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Department</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->department?->name ?? '-' }}</p>
                </div>
                <div class="py-2 rounded-lg bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Job Title</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->job_title ?? '-' }}</p>
                </div>
                <div class="py-2 rounded-lg bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Salary</p>
                    <p class="mt-2 font-semibold text-slate-900">
                        {{ $employee->salary !== null ? '$'.number_format((float) $employee->salary, 2) : '-' }}
                    </p>
                </div>
                <div class="py-2 rounded-lg bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Hire Date</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $employee->hired_at?->format('M d, Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid gap-4 md:grid-cols-2">
            <a href="{{ route('employee.attendance.index') }}" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
                <p class="text-xs uppercase tracking-wide text-slate-500">View Records</p>
                <p class="mt-2 text-sm font-semibold text-slate-900">Attendance</p>
            </a>

            <a href="{{ route('employee.leaves.index') }}" class="group rounded-lg border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
                <p class="text-xs uppercase tracking-wide text-slate-500">View Requests</p>
                <p class="mt-2 text-sm font-semibold text-slate-900">Leave Requests</p>
            </a>
        </div>
    </div>
</x-app-layout>
