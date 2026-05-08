<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Self Service</p>
                <h2 class="text-3xl font-bold text-slate-900">Leave Requests</h2>
            </div>
            <a href="{{ route('employee.leaves.create') }}" class="rounded-full bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-500 transition">
                + New Request
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($leaves->count() > 0)
            <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Type</th>
                            <th class="px-6 py-3 text-left">Period</th>
                            <th class="px-6 py-3 text-left">Days</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Requested</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3">
                                    <span class="font-medium text-slate-900">
                                        @switch($leave->leave_type)
                                            @case('annual')
                                                🏖 Annual
                                                @break
                                            @case('sick')
                                                🏥 Sick
                                                @break
                                            @case('unpaid')
                                                💼 Unpaid
                                                @break
                                            @default
                                                📋 {{ ucfirst($leave->leave_type) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-slate-600">
                                    {{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $leave->days_count }} days</td>
                                <td class="px-6 py-3">
                                    @if($leave->status === 'pending')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                                            ⏳ Pending
                                        </span>
                                    @elseif($leave->status === 'approved')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            ✓ Approved
                                        </span>
                                    @elseif($leave->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-700">
                                            ✗ Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-slate-600 text-xs">
                                    {{ $leave->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $leaves->links() }}
                </div>
            </div>
        @else
            <div class="rounded-lg border border-slate-200 bg-white p-12 text-center">
                <p class="text-slate-500 text-sm">No leave requests yet.</p>
                <a href="{{ route('employee.leaves.create') }}" class="mt-4 inline-block rounded-full bg-cyan-600 px-5 py-2 text-sm font-semibold text-white hover:bg-cyan-500">
                    Create Your First Request
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
