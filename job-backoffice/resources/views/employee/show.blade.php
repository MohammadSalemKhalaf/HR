<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Employee</p>
                <h2 class="text-2xl font-bold text-slate-900">{{ $employee->user?->name ?? 'Employee' }}</h2>
                <p class="text-sm text-slate-600">{{ $employee->job_title ?? '' }} • {{ $employee->department?->name ?? '' }}</p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('employees.checkin', $employee->id) }}" method="POST">
                    @csrf
                    <button class="rounded-full bg-emerald-600 px-4 py-2 text-white font-semibold">Check In</button>
                </form>

                <form action="{{ route('employees.checkout', $employee->id) }}" method="POST">
                    @csrf
                    <button class="rounded-full bg-rose-600 px-4 py-2 text-white font-semibold">Check Out</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="mt-6 space-y-6">
        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700">{{ session('error') }}</div>
        @endif

        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-6">
            <h3 class="text-lg font-semibold text-slate-900">Attendance</h3>
            <p class="text-sm text-slate-500">Recent records</p>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Check In</th>
                            <th class="px-4 py-2 text-left">Check Out</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($employee->attendanceRecords as $rec)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $rec->attendance_date->toDateString() }}</td>
                                <td class="px-4 py-3">{{ $rec->check_in_at ? $rec->check_in_at->toTimeString() : '-' }}</td>
                                <td class="px-4 py-3">{{ $rec->check_out_at ? $rec->check_out_at->toTimeString() : '-' }}</td>
                                <td class="px-4 py-3">{{ ucfirst($rec->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-500">No attendance records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-6">
            <h3 class="text-lg font-semibold text-slate-900">Request Leave</h3>
            <p class="text-sm text-slate-500">Submit a leave request for this employee.</p>

            <div class="mt-4">
                <a href="{{ route('leave.create', ['employeeId' => $employee->id]) }}" class="rounded-full bg-cyan-600 px-4 py-2 text-white font-semibold">Request Leave</a>
            </div>
        </div>
    </div>
</x-app-layout>
