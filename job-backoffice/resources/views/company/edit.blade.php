@php
if(auth()->user()->hasRole('admin')) {
    $formAction = route('companies.update', $company->id);
} else {
    $formAction = route('my-company.update');
}
@endphp

<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Company profile</p>
            <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit Company</h2>
            <p class="mt-2 text-sm text-slate-600">Update company information below.</p>
        </div>

        <a href="{{ auth()->user()->hasRole('admin') ? route('companies.show', $company->id) : route('my-company.show') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Back
        </a>
    </div>
</x-slot>

<div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
    <form action="{{ $formAction }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Company Name *</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Industry *</label>
                <input type="text" name="industry" value="{{ old('industry', $company->industry) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                @error('industry')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Address *</label>
                <input type="text" name="address" value="{{ old('address', $company->address) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                @error('address')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Website</label>
                <input type="url" name="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                @error('website')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Owner</label>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                {{ $company->owner->name ?? '-' }}
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
            <a href="{{ auth()->user()->hasRole('admin') ? route('companies.show', $company->id) : route('my-company.show') }}" class="rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </a>

            <button type="submit" class="rounded-full bg-cyan-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                Update Company
            </button>
        </div>
    </form>

    <div class="mt-6 rounded-3xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-800">
        Updating company details will affect all jobs associated with this company.
    </div>
</div>
</x-app-layout>k
