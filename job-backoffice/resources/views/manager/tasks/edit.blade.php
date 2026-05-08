@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Edit Task</h2>

    <form action="{{ route('manager.tasks.update', $task->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="block mb-1">Title</label>
            <input name="title" value="{{ $task->title }}" class="w-full border p-2 rounded" required />
        </div>

        <div class="mb-3">
            <label class="block mb-1">Assign to</label>
            <select name="employee_id" class="w-full border p-2 rounded" required>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" {{ $task->employee_id===$e->id ? 'selected' : '' }}>{{ $e->user?->name }} ({{ $e->department?->name }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Priority</label>
            <select name="priority" class="w-full border p-2 rounded">
                <option value="low" {{ $task->priority==='low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ $task->priority==='medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ $task->priority==='high' ? 'selected' : '' }}>High</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="pending" {{ $task->status==='pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $task->status==='in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $task->status==='completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Repository URL</label>
            <input name="repository_url" value="{{ $task->repository_url }}" class="w-full border p-2 rounded" />
        </div>

        <div class="mb-3">
            <label class="block mb-1">Due Date</label>
            <input type="date" name="due_date" value="{{ $task->due_date?->toDateString() }}" class="w-full border p-2 rounded" />
        </div>

        <div class="mb-3">
            <label class="block mb-1">Description</label>
            <textarea name="description" class="w-full border p-2 rounded">{{ $task->description }}</textarea>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
