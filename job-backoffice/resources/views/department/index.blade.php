<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Departments</h2>
                <p class="mt-2 text-sm text-slate-600">Manage your company departments and assign managers.</p>
            </div>

            <a href="{{ route('departments.create') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                Add Department
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="overflow-x-auto rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Department Name</th>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-left">Manager</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($departments as $department)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                <a href="{{ route('departments.show', $department->id) }}" class="hover:text-cyan-700 hover:underline">
                                    {{ $department->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $department->code ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($department->manager?->user)
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ $department->manager->user->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('departments.show', $department->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">View</a>
                                    <a href="{{ route('departments.edit', $department->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Edit</a>
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-rose-600 hover:text-rose-700" onclick="return confirm('Delete this department?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">No departments found. Create one to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departments->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $departments->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
