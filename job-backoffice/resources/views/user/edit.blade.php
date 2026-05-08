<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit User</h2>
                <p class="mt-2 text-sm text-slate-600">Update the user's password while keeping identity and role intact.</p>
            </div>

            <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur sm:p-8">
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
                        <input type="text" value="{{ $user->name }}" class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500" readonly>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input type="text" value="{{ $user->email }}" class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500" readonly>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
                        <input type="text" value="{{ $user->roleName() }}" class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500" readonly>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">New Password</label>
                        <input type="password" name="password" class="w-full rounded-2xl border {{ $errors->has('password') ? 'border-rose-300 bg-rose-50' : 'border-slate-300 bg-white' }} px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('password')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('users.index') }}" class="rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
