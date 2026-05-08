<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Management</p>
                <h2 class="text-3xl font-bold text-slate-900">Employees</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($employees->count() > 0)
            <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Department</th>
                            <th class="px-6 py-3 text-left">Title</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($employees as $employee)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3">
                                    <a href="{{ route('manager.employees.show', $employee->id) }}" class="font-semibold text-cyan-600 hover:text-cyan-700">
                                        {{ $employee->user?->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $employee->user?->email ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('manager.departments.show', $employee->department?->id) }}" class="text-slate-600 hover:text-slate-900 underline">
                                        {{ $employee->department?->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $employee->job_title ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    @if($employee->status === 'active')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                            ✓ Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            — {{ ucfirst($employee->status) }}
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
            </div>
        @else
            <div class="rounded-lg border border-slate-200 bg-white p-12 text-center">
                <p class="text-slate-500 text-sm">No employees in your departments yet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
