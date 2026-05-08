<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Management</p>
                <h2 class="text-3xl font-bold text-slate-900">{{ $department->name }}</h2>
            </div>
            <a href="{{ route('manager.departments.index') }}" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">← Back to Departments</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Department Info Card -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Department Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Department Name</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $department->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Department Code</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $department->code ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Manager</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $department->manager?->user?->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">Employees</h3>
                    <span class="text-sm text-slate-500">
                        @if($employees->total() > 0)
                            {{ $employees->total() }} employee{{ $employees->total() !== 1 ? 's' : '' }}
                        @endif
                    </span>
                </div>
            </div>

            @if($employees->count() > 0)
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Title</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($employees as $emp)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3">
                                    <a href="{{ route('manager.employees.show', $emp->id) }}" class="font-semibold text-cyan-600 hover:text-cyan-700">
                                        {{ $emp->user?->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $emp->user?->email ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $emp->job_title ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    @if($emp->status === 'active')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            ✓ Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            {{ ucfirst($emp->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $employees->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-slate-500 text-sm">No employees in this department yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
