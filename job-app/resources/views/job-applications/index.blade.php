<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">My Applications</h2>

                    @forelse($applications as $application)
                        <div class="mb-6 pb-6 border-b border-gray-200 last:border-b-0">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Job and Company Info -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $application->jobvacancy->title ?? 'Unknown Job' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $application->jobvacancy->company->name ?? 'Unknown Company' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Applied {{ $application->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Status and Resume -->
                                <div>
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($application->status === 'accepted')
                                                bg-green-100 text-green-800
                                            @elseif($application->status === 'rejected')
                                                bg-red-100 text-red-800
                                            @else
                                                bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700">
                                        <strong>Resume:</strong> {{ $application->resume->filename ?? 'N/A' }}
                                    </p>
                                </div>

                                <!-- AI Score and Feedback -->
                                <div>
                                    <div class="mb-2">
                                        <p class="text-sm text-gray-700">
                                            <strong>AI Match Score:</strong>
                                            <span class="text-lg font-bold text-indigo-600">
                                                {{ $application->aiGeneratedScore ?? 0 }}/10
                                            </span>
                                        </p>
                                    </div>
                                    @if($application->aiGeneratedFeedback)
                                        <p class="text-sm text-gray-600 italic bg-gray-50 p-2 rounded">
                                            {{ $application->aiGeneratedFeedback }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500 italic">
                                            Analysis pending...
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-600 mb-4">You haven't applied for any jobs yet.</p>
                            <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Browse Jobs
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
