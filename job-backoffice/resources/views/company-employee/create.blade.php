<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Employees</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Create New Employee</h2>
            </div>

            <a href="{{ route('company-employees.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
            <form action="{{ route('company-employees.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Full Name *</label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="John Doe" class="w-full rounded-2xl border {{ $errors->has('name') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Email *</label>
                        <input type="email" name="email" required value="{{ old('email') }}" placeholder="john@example.com" class="w-full rounded-2xl border {{ $errors->has('email') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('email')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Password *</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full rounded-2xl border {{ $errors->has('password') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('password')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Role Type *</label>
                        <select name="role_type" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                            <option value="">-- Select Role --</option>
                            <option value="employee" {{ old('role_type') === 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="manager" {{ old('role_type') === 'manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                        @error('role_type')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Department</label>
                        <select name="department_id" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Job Title</label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}" placeholder="e.g., Senior Developer" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Salary</label>
                        <input type="number" step="0.01" name="salary" value="{{ old('salary') }}" placeholder="0.00" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Hire Date</label>
                        <input type="date" name="hired_at" value="{{ old('hired_at') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('company-employees.index') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
