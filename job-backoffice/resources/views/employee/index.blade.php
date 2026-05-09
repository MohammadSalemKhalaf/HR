<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">HR</p>
                <h2 class="text-2xl font-bold text-slate-900">Employees</h2>
            </div>

            <a href="{{ route('employees.list') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Refresh</a>
        </div>
    </x-slot>

    <div class="mt-6">
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Company</th>
                        <th class="px-4 py-3 text-left">Department</th>
                        <th class="px-4 py-3 text-left">Job Title</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($employees as $emp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $emp->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $emp->company?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $emp->department?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $emp->job_title ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('employees.view', $emp->id) }}" class="text-cyan-700 font-semibold">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </div>
</x-app-layout>
