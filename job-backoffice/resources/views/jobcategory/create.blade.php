<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Add New Job Category</h2>
                <p class="mt-2 text-sm text-slate-600">Create a clean category for vacancies and reports.</p>
            </div>

            <a href="{{ route('job-categories.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
            <form action="{{ route('job-categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Category Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g., Information Technology" class="w-full rounded-2xl border {{ $errors->has('name') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-2 text-xs text-slate-500">Category names should be unique and descriptive.</p>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('job-categories.index') }}" class="rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>

                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Save Category
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-4 rounded-3xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
            Examples: Technology, Healthcare, Finance, Education, Retail, Construction.
        </div>
    </div>
</x-app-layout>