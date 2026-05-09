@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">{{ $task->title }}</h2>

    <div class="p-4 border rounded mb-4">
        <div><strong>Manager:</strong> {{ $task->manager->user?->name }}</div>
        <div><strong>Department:</strong> {{ $task->department?->name }}</div>
        <div><strong>Due:</strong> {{ $task->due_date?->toDateString() ?? '-' }}</div>
        <div class="mt-3">{{ $task->description ?? '-' }}</div>
        @if($task->repository_url)
            <div class="mt-3"><a href="{{ $task->repository_url }}" target="_blank">Repository</a></div>
        @endif
    </div>

    <form action="{{ route('employee.tasks.update_status', $task->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="pending" {{ $task->status==='pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $task->status==='in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $task->status==='completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Completion Note (optional)</label>
            <textarea name="completion_note" class="w-full border p-2 rounded"></textarea>
        </div>

        <button class="btn btn-primary">Update Status</button>
    </form>
</div>
@endsection
