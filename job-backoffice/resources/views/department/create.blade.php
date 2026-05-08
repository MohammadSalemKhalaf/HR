<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Departments</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Create Department</h2>
            </div>

            <a href="{{ route('departments.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
            <form action="{{ route('departments.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Department Name *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g., Engineering" class="w-full rounded-2xl border {{ $errors->has('name') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Department Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g., ENG" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    @error('code')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Assign Manager</label>
                    <select name="manager_employee_id" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        <option value="">-- Select Manager --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('manager_employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->user?->name ?? 'Unknown' }} ({{ $emp->job_title ?? 'No Title' }})
                            </option>
                        @endforeach
                    </select>
                    @error('manager_employee_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('departments.index') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
