@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Create Task</h2>

    <form action="{{ route('manager.tasks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="block mb-1">Title</label>
            <input name="title" class="w-full border p-2 rounded" required />
        </div>

        <div class="mb-3">
            <label class="block mb-1">Assign to</label>
            <select name="employee_id" class="w-full border p-2 rounded" required>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}">{{ $e->user?->name }} ({{ $e->department?->name }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Priority</label>
            <select name="priority" class="w-full border p-2 rounded">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Repository URL</label>
            <input name="repository_url" class="w-full border p-2 rounded" />
        </div>

        <div class="mb-3">
            <label class="block mb-1">Due Date</label>
            <input type="date" name="due_date" class="w-full border p-2 rounded" />
        </div>

        <div class="mb-3">
            <label class="block mb-1">Description</label>
            <textarea name="description" class="w-full border p-2 rounded"></textarea>
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
