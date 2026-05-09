@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Tasks</h2>
        <a href="{{ route('manager.tasks.create') }}" class="btn btn-primary">Create Task</a>
    </div>

    <div class="space-y-4">
        @foreach($tasks as $task)
            <div class="p-4 border rounded-lg flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ $task->title }}</div>
                    <div class="text-sm text-slate-600">{{ $task->employee->user?->name }} · {{ $task->department->name ?? '-' }}</div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-2 py-1 rounded {{ $task->priority==='high' ? 'bg-red-200' : ($task->priority==='medium' ? 'bg-yellow-200' : 'bg-green-200') }}">{{ ucfirst($task->priority) }}</div>
                    <div class="px-2 py-1 rounded {{ $task->status==='completed' ? 'bg-green-200' : ($task->status==='in_progress' ? 'bg-blue-200' : 'bg-gray-200') }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</div>
                    <a href="{{ route('manager.tasks.show', $task->id) }}" class="text-sm text-blue-600">View</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $tasks->links() }}</div>
</div>
@endsection
