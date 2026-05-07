<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Administration</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                    Job Categories
                    @if(request('archived') == 'true')
                        <span class="ml-2 text-sm font-medium text-slate-500">(Archived)</span>
                    @endif
                </h2>
                <p class="mt-2 text-sm text-slate-600">Organize vacancies into clear hiring categories.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if(request('archived') == 'true')
                    <a href="{{ route('job-categories.index') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Active Categories
                    </a>
                @else
                    <a href="{{ route('job-categories.index', ['archived' => 'true']) }}" class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Archived Categories
                    </a>

                    <a href="{{ route('job-categories.create') }}" class="rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-500">
                        Add Category
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="rounded-[2rem] border border-white/70 bg-white/85 shadow-lg shadow-slate-950/5 backdrop-blur overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <p class="text-sm font-medium text-slate-500">Categories List</p>
            <h3 class="text-lg font-semibold text-slate-900">Total {{ $categories->total() }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Category Name</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if(request('archived') == 'true')
                                        <form action="{{ route('job-categories.restore', $category->id) }}" method="POST" onsubmit="return confirm('Restore this category?')">
                                            @csrf
                                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700">Restore</button>
                                        </form>
                                    @else
                                        <a href="{{ route('job-categories.edit', $category->id) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Edit</a>
                                        <form action="{{ route('job-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="2" class="px-6 py-12 text-center text-slate-500">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>