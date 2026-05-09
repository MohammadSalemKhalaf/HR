<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Operations</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit Job Vacancy</h2>
                <p class="mt-2 text-sm text-slate-600">Update vacancy information and keep the workflow aligned.</p>
            </div>

            <a href="{{ route('job-vacancies.show', $jobVacancy->id) }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
            <form action="{{ route('job-vacancies.update', $jobVacancy->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Title *</label>
                        <input type="text" name="title" value="{{ old('title', $jobVacancy->title) }}" class="w-full rounded-2xl border {{ $errors->has('title') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('title')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Location *</label>
                        <input type="text" name="location" value="{{ old('location', $jobVacancy->location) }}" class="w-full rounded-2xl border {{ $errors->has('location') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('location')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Salary *</label>
                        <input type="number" name="salary" value="{{ old('salary', $jobVacancy->salary) }}" class="w-full rounded-2xl border {{ $errors->has('salary') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('salary')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Type *</label>
                        <select name="type" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                            <option value="">Select Type</option>
                            @foreach(['full-time','contract','hybrid','remote'] as $type)
                                <option value="{{ $type }}" {{ old('type', $jobVacancy->type) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Company *</label>
                        <select name="companyId" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('companyId', $jobVacancy->companyId) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('companyId')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Category *</label>
                        <select name="categoryId" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('categoryId', $jobVacancy->categoryId) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('categoryId')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Description *</label>
                    <textarea name="description" rows="5" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">{{ old('description', $jobVacancy->description) }}</textarea>
                    @error('description')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('job-vacancies.show', $jobVacancy->id) }}" class="rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>