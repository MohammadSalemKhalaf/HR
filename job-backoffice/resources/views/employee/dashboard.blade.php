<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Employee Area</p>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Employee Dashboard</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-[2rem] border border-white/70 bg-white/85 p-6 shadow-lg shadow-slate-950/5 backdrop-blur">
            <p class="text-sm text-slate-600">Use the actions below to manage your daily attendance.</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="button" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">
                    Check In
                </button>

                <button type="button" class="rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                    Check Out
                </button>
            </div>
        </div>
    </div>
</x-app-layout>