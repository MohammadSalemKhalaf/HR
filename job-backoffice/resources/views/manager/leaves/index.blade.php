<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Management</p>
                <h2 class="text-3xl font-bold text-slate-900">Leave Requests</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($leaves->count() > 0)
            <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Employee</th>
                            <th class="px-6 py-3 text-left">Type</th>
                            <th class="px-6 py-3 text-left">Period</th>
                            <th class="px-6 py-3 text-left">Days</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3">
                                    <span class="font-medium text-slate-900">{{ $leave->employee->user?->name ?? '-' }}</span>
                                    <p class="text-xs text-slate-500 mt-1">{{ $leave->employee->job_title }}</p>
                                </td>
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
                                <td class="px-6 py-3 text-right">
                                    @if($leave->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('manager.leaves.approve', $leave->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-200 transition">
                                                    ✓ Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('manager.leaves.reject', $leave->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-700 hover:bg-rose-200 transition">
                                                    ✗ Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">—</span>
                                    @endif
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
                <p class="text-slate-500 text-sm">No leave requests from your departments.</p>
            </div>
        @endif
    </div>
</x-app-layout>
