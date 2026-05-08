<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Manager Area</p>
                <h2 class="text-3xl font-bold text-slate-900">Manager Dashboard</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
                <p class="text-sm text-slate-500">Departments</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $departmentsCount }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
                <p class="text-sm text-slate-500">Employees</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $employeesCount }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
                <p class="text-sm text-slate-500">Pending Leaves</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $pendingLeaves }}</p>
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-5">
                <p class="text-sm text-slate-500">Today Attendance</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $todayAttendance }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
