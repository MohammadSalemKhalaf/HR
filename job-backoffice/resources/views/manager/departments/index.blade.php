<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Management</p>
                <h2 class="text-3xl font-bold text-slate-900">My Departments</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($departments->count() > 0)
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($departments as $department)
                    <div class="rounded-lg border border-slate-200 bg-white p-6 hover:shadow-lg transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('manager.departments.show', $department->id) }}" class="text-lg font-bold text-slate-900 hover:text-cyan-600">
                                    {{ $department->name }}
                                </a>
                                @if($department->code)
                                    <p class="mt-1 text-sm text-slate-500">Code: {{ $department->code }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">
                                    {{ \App\Models\Employee::where('department_id', $department->id)->count() }}
                                </span>
                                Employees
                            </p>
                            <a href="{{ route('manager.departments.show', $department->id) }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                                View →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $departments->links() }}
            </div>
        @else
            <div class="rounded-lg border border-slate-200 bg-white p-12 text-center">
                <p class="text-slate-500 text-sm">You don't manage any departments yet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
