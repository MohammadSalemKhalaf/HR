<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                    Companies
                    @if(request('archived') == 'true')
                        <span class="ml-2 text-sm font-medium text-slate-500">(Archived)</span>
                    @endif
                </h2>
                <p class="mt-2 text-sm text-slate-600">Manage ownership, company profiles, and archived records.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if(request('archived') == 'true')
                    <a href="{{ route('companies.index') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Active Companies
                    </a>
                @else
                    <a href="{{ route('companies.index', ['archived' => 'true']) }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Archived Companies
                    </a>

                    <a href="{{ route('companies.create') }}" class="rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Add Company
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Companies List</p>
                    <h3 class="text-lg font-semibold text-slate-900">Total {{ $companies->total() }}</h3>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Industry</th>
                        <th class="px-6 py-3 text-left">Address</th>
                        <th class="px-6 py-3 text-left">Website</th>
                        <th class="px-6 py-3 text-left">Owner</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse ($companies as $company)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                <a href="{{ route('companies.show', $company->id) }}" class="hover:text-cyan-700 hover:underline">
                                    {{ $company->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $company->industry }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $company->address }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="text-cyan-700 hover:underline">{{ $company->website }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $company->owner?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if(request('archived') == 'true')
                                        <form action="{{ route('companies.restore', $company->id) }}" method="POST" onsubmit="return confirm('Restore this company?')">
                                            @csrf
                                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700">Restore</button>
                                        </form>
                                    @else
                                        <a href="{{ route('companies.edit', $company->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Edit</a>
                                        <form action="{{ route('companies.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-600 hover:text-rose-700">Archive</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">No companies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $companies->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>