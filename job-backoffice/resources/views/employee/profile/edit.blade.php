<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Self Service</p>
                <h2 class="text-3xl font-bold text-slate-900">Edit Profile</h2>
            </div>
            <a href="{{ route('employee.profile.show') }}" class="text-sm text-cyan-600 hover:text-cyan-700 font-semibold">← Back to Profile</a>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="font-semibold text-slate-900">Edit Your Information</h3>
                <p class="text-xs text-slate-500 mt-1">You can only edit your name, email, and password. Other information must be changed by your manager.</p>
            </div>

            <form action="{{ route('employee.profile.update') }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $employee->user?->name) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $employee->user?->email) }}" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Section -->
                <div class="border-t border-slate-200 pt-6">
                    <h4 class="font-semibold text-slate-900 mb-4">Change Password</h4>
                    <p class="text-xs text-slate-500 mb-4">Leave blank to keep your current password</p>

                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">New Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500 @error('password') border-rose-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="border-t border-slate-200 pt-6 flex items-center justify-between">
                    <a href="{{ route('employee.profile.show') }}" class="rounded-lg border border-slate-300 px-6 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-lg bg-cyan-600 px-6 py-2 text-sm font-semibold text-white hover:bg-cyan-500 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
