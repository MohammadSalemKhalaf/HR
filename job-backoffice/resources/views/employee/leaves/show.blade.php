<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Self Service</p>
                <h2 class="text-3xl font-bold text-slate-900">Leave Request Details</h2>
            </div>
            <a href="{{ route('employee.leaves.index') }}" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">← Back to Leaves</a>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <!-- Leave Information -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Leave Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Leave Type</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">
                        @switch($leave->leave_type)
                            @case('annual')
                                🏖 Annual Leave
                                @break
                            @case('sick')
                                🏥 Sick Leave
                                @break
                            @case('unpaid')
                                💼 Unpaid Leave
                                @break
                            @default
                                📋 {{ ucfirst($leave->leave_type) }}
                        @endswitch
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                    <p class="mt-2">
                        @if($leave->status === 'pending')
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700">
                                ⏳ Pending Review
                            </span>
                        @elseif($leave->status === 'approved')
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700">
                                ✓ Approved
                            </span>
                        @elseif($leave->status === 'rejected')
                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-sm font-medium text-rose-700">
                                ✗ Rejected
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Period Information -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Leave Period</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Start Date</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $leave->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">End Date</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $leave->end_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Number of Days</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $leave->days_count }} day{{ $leave->days_count !== 1 ? 's' : '' }}</p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Additional Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Requested On</p>
                    <p class="text-sm text-slate-600">{{ $leave->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>
                @if($leave->approved_at)
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">{{ ucfirst($leave->status) }} On</p>
                        <p class="text-sm text-slate-600">{{ $leave->approved_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">{{ ucfirst($leave->status) }} By</p>
                        <p class="text-sm text-slate-600">{{ $leave->approvedBy?->name ?? '-' }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action -->
        <div class="flex items-center justify-end">
            <a href="{{ route('employee.leaves.index') }}" class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                Back to List
            </a>
        </div>
    </div>
</x-app-layout>
