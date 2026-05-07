<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Leaves</p>
                <h2 class="text-2xl font-bold text-slate-900">Leave Requests</h2>
            </div>

            <a href="{{ route('leave.create') }}" class="rounded-full bg-cyan-600 px-4 py-2 text-white font-semibold">New Request</a>
        </div>
    </x-slot>

    <div class="mt-6">
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Employee</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Period</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($leaves as $leave)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $leave->employee->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($leave->leave_type) }}</td>
                            <td class="px-4 py-3">{{ $leave->start_date->toDateString() }} — {{ $leave->end_date->toDateString() }}</td>
                            <td class="px-4 py-3">{{ ucfirst($leave->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $leaves->links() }}
        </div>
    </div>
</x-app-layout>
