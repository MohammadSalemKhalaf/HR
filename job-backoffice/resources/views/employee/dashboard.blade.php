<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Employee Area</p>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-sm">
                <p class="text-sm text-slate-500">Department</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $employee->department?->name ?? 'Not Assigned' }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-sm">
                <p class="text-sm text-slate-500">This Month Hours</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ floor($monthHours / 60) }}h {{ $monthHours % 60 }}m</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5 shadow-sm">
                <p class="text-sm text-slate-500">Leave Requests</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $leaveCount }}</p>
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
            <h3 class="text-lg font-bold text-slate-900">Today's Attendance</h3>

            @if($todayRecord)
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <span class="text-sm text-slate-600">Check In</span>
                        <span class="font-semibold text-slate-900">{{ $todayRecord->check_in_at?->toTimeString() ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <span class="text-sm text-slate-600">Check Out</span>
                        <span class="font-semibold text-slate-900">{{ $todayRecord->check_out_at?->toTimeString() ?? '-' }}</span>
                    </div>
                    @if($todayRecord->check_in_at && $todayRecord->check_out_at)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                            <span class="text-sm text-emerald-700">Worked</span>
                            <span class="font-semibold text-emerald-900">{{ $todayRecord->check_out_at->diffForHumans($todayRecord->check_in_at, true) }}</span>
                        </div>
                    @endif
                </div>
            @else
                <p class="mt-4 text-sm text-slate-600">No check-in today yet.</p>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                @if(!$todayRecord?->check_in_at)
                    <form action="{{ route('employee.attendance.checkin') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            ✓ Check In
                        </button>
                    </form>
                @endif

                @if($todayRecord?->check_in_at && !$todayRecord?->check_out_at)
                    <form action="{{ route('employee.attendance.checkout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-full bg-rose-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                            ✗ Check Out
                        </button>
                    </form>
                @endif

                @if($todayRecord?->check_in_at && $todayRecord?->check_out_at)
                    <span class="rounded-full bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-600">
                        ✓ Day completed
                    </span>
                @endif
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid gap-4 md:grid-cols-2">
            <a href="{{ route('employee.attendance.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">View Attendance</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">Attendance History</p>
                    </div>
                    <span class="text-2xl">📋</span>
                </div>
            </a>

            <a href="{{ route('employee.leaves.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:bg-cyan-50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">View Leaves</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">Leave Requests</p>
                    </div>
                    <span class="text-2xl">📅</span>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
