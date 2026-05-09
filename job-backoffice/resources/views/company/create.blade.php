<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Add New Company</h2>
                <p class="mt-2 text-sm text-slate-600">Create the company profile and attach or create an owner account.</p>
            </div>
        </div>
    </x-slot>

    <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
        <form action="{{ route('companies.store') }}" method="POST" class="space-y-8">
            @csrf

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
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Industry *</label>
                    <select name="industry" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        <option value="">Select industry</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry }}" {{ old('industry') === $industry ? 'selected' : '' }}>{{ $industry }}</option>
                        @endforeach
                    </select>
                    @error('industry')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Address *</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    @error('address')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Website</label>
                    <input type="url" name="website" value="{{ old('website') }}" placeholder="https://example.com" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    @error('website')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="mb-4">
                        <h3 class="text-base font-semibold text-slate-900">Attach Existing Owner</h3>
                        <p class="mt-1 text-sm text-slate-500">Use an existing user account as the company owner.</p>
                    </div>

                    <select name="owner_id" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        <option value="">Select owner (optional)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="mb-4">
                        <h3 class="text-base font-semibold text-slate-900">Create New Owner</h3>
                        <p class="mt-1 text-sm text-slate-500">Leave owner selection empty and fill these fields to create a new company owner.</p>
                    </div>

                    <div class="space-y-4">
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" placeholder="Owner name" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Owner email" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        <input type="password" name="password" placeholder="Password" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>
                    @error('email')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    @error('password')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                <a href="{{ route('companies.index') }}" class="rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="rounded-full bg-cyan-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                    Save Company
                </button>
            </div>
        </form>
    </div>
</x-app-layout>