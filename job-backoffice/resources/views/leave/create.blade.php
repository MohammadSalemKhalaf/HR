<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Leave</p>
                <h2 class="text-2xl font-bold text-slate-900">Request Leave</h2>
            </div>

            <a href="{{ route('leave.index') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Back</a>
        </div>
    </x-slot>

    <div class="mt-6 mx-auto max-w-2xl">
        <div class="rounded-lg border border-slate-200 bg-white p-6">
            <form action="{{ route('leave.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700">Employee</label>
                    <input type="text" disabled value="{{ optional($employeeId ? \App\Models\Employee::find($employeeId) : null)->user?->name ?? 'Select employee' }}" class="w-full rounded-md border px-3 py-2 bg-slate-50">
                    <input type="hidden" name="employee_id" value="{{ $employeeId ?? '' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Leave Type</label>
                    <select name="leave_type" class="w-full rounded-md border px-3 py-2">
                        <option value="annual">Annual</option>
                        <option value="sick">Sick</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Start Date</label>
                        <input type="date" name="start_date" class="w-full rounded-md border px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">End Date</label>
                        <input type="date" name="end_date" class="w-full rounded-md border px-3 py-2">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Notes</label>
                    <textarea name="notes" rows="4" class="w-full rounded-md border px-3 py-2"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('leave.index') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</a>
                    <button type="submit" class="rounded-full bg-cyan-600 px-4 py-2 text-white font-semibold">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
