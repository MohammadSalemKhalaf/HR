@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">My Tasks</h2>

    <div class="space-y-4">
        @foreach($tasks as $task)
            <div class="p-4 border rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold">{{ $task->title }}</div>
                        <div class="text-sm text-slate-600">{{ $task->manager->user?->name }} · {{ $task->department?->name }}</div>
                    </div>
                    <div class="text-sm text-slate-500">Due: {{ $task->due_date?->toDateString() ?? '-' }}</div>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <div>
                        <span class="px-2 py-1 rounded {{ $task->priority==='high' ? 'bg-red-200' : ($task->priority==='medium' ? 'bg-yellow-200' : 'bg-green-200') }}">{{ ucfirst($task->priority) }}</span>
                        <span class="px-2 py-1 rounded {{ $task->status==='completed' ? 'bg-green-200' : ($task->status==='in_progress' ? 'bg-blue-200' : 'bg-gray-200') }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
                    </div>
                    <div>
                        <a href="{{ route('employee.tasks.show', $task->id) }}" class="text-blue-600">View</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
