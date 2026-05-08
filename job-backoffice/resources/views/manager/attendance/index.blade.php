<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Management</p>
                <h2 class="text-3xl font-bold text-slate-900">Attendance Records</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Card -->
        <div class="rounded-lg border border-slate-200 bg-white p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[250px]">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Filter by Date</label>
                    <input type="date" name="date" value="{{ $date }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                </div>
                <button type="submit" class="rounded-lg bg-cyan-600 px-6 py-2 text-sm font-semibold text-white hover:bg-cyan-500 transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">
                    Attendance for {{ date('F d, Y', strtotime($date)) }}
                </h3>
            </div>

            @if($records->count() > 0)
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Employee</th>
                            <th class="px-6 py-3 text-left">Department</th>
                            <th class="px-6 py-3 text-left">Check In</th>
                            <th class="px-6 py-3 text-left">Check Out</th>
                            <th class="px-6 py-3 text-left">Hours</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($records as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3">
                                    <a href="{{ route('manager.employees.show', $r->employee->id) }}" class="font-semibold text-cyan-600 hover:text-cyan-700">
                                        {{ $r->employee->user?->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $r->employee->department?->name ?? '-' }}</td>
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
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-slate-500 text-sm">No attendance records for the selected date.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
