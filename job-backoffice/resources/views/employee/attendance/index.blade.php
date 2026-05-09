<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Self Service</p>
                <h2 class="text-3xl font-bold text-slate-900">My Attendance</h2>
            </div>
            <a href="{{ route('employee.dashboard') }}" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">← Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Quick Actions -->
        @php
            $today = now()->toDateString();
            $todayRecord = $records->first(fn($r) => $r->attendance_date->toDateString() === $today);
        @endphp

        @if($todayRecord)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-emerald-900">Today's Status</h3>
                        <div class="mt-3 space-y-2">
                            <p class="text-sm text-emerald-700">
                                ✓ Check In: <span class="font-semibold">{{ $todayRecord->check_in_at?->toTimeString() ?? 'Not checked in' }}</span>
                            </p>
                            <p class="text-sm text-emerald-700">
                                ✗ Check Out: <span class="font-semibold">{{ $todayRecord->check_out_at?->toTimeString() ?? 'Not checked out' }}</span>
                            </p>
                            @if($todayRecord->check_in_at && $todayRecord->check_out_at)
                                <p class="text-sm font-semibold text-emerald-900">
                                    ⏱ Worked: {{ $todayRecord->check_out_at->diffForHumans($todayRecord->check_in_at, true) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- History Table -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Attendance History</h3>
            </div>

            @if($records->count() > 0)
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Check In</th>
                            <th class="px-6 py-3 text-left">Check Out</th>
                            <th class="px-6 py-3 text-left">Hours</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($records as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3 text-slate-900 font-medium">{{ $r->attendance_date->format('M d, Y') }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $r->check_in_at?->toTimeString() ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $r->check_out_at?->toTimeString() ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-600">
                                    @if($r->check_in_at && $r->check_out_at)
                                        {{ sprintf('%.1f h', $r->check_out_at->diffInMinutes($r->check_in_at) / 60) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($r->check_in_at && $r->check_out_at)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            ✓ Complete
                                        </span>
                                    @elseif($r->check_in_at)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                                            ⏱ In Progress
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            — Not Started
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $records->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-slate-500 text-sm">No attendance records yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
