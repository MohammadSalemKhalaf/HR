@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">{{ $task->title }}</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('manager.tasks.edit', $task->id) }}" class="btn">Edit</a>
            <form method="POST" action="{{ route('manager.tasks.destroy', $task->id) }}">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form>
        </div>
    </div>

    <div class="p-4 border rounded">
        <div class="mb-2"><strong>Assigned to:</strong> {{ $task->employee->user?->name }}</div>
        <div class="mb-2"><strong>Department:</strong> {{ $task->department?->name }}</div>
        <div class="mb-2"><strong>Priority:</strong> {{ ucfirst($task->priority) }}</div>
        <div class="mb-2"><strong>Status:</strong> {{ ucfirst(str_replace('_',' ',$task->status)) }}</div>
        <div class="mb-2"><strong>Due:</strong> {{ $task->due_date?->toDateString() ?? '-' }}</div>
        <div class="mt-4"><strong>Description</strong><div class="mt-2">{{ $task->description ?? '-' }}</div></div>
        @if($task->repository_url)
            <div class="mt-4"><a href="{{ $task->repository_url }}" target="_blank">Repository</a></div>
        @endif
    </div>
</div>
@endsection
