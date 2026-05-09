<?php

namespace App\Http\Controllers\EmployeeArea;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\EmployeeTask;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskCompleted;

class TaskController extends Controller
{
    private function employeeId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->employee?->id;
    }

    public function index()
    {
        $employeeId = $this->employeeId();
        if (!$employeeId) abort(403);

        $tasks = EmployeeTask::where('employee_id', $employeeId)
            ->with(['manager.user','department'])
            ->latest()
            ->get();

        return view('employee.tasks.index', compact('tasks'));
    }

    public function show(string $id)
    {
        $employeeId = $this->employeeId();
        if (!$employeeId) abort(403);

        $task = EmployeeTask::with(['manager.user','department'])->findOrFail($id);
        if ($task->employee_id !== $employeeId) abort(403);

        return view('employee.tasks.show', compact('task'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $employeeId = $this->employeeId();
        if (!$employeeId) abort(403);

        $task = EmployeeTask::findOrFail($id);
        if ($task->employee_id !== $employeeId) abort(403);

        $validated = $request->validate([
            'status' => ['required','in:pending,in_progress,completed'],
            'completion_note' => ['nullable','string'],
        ]);

        $old = $task->status;

        $task->status = $validated['status'];
        if ($validated['status'] === 'completed') {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        $task->save();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        app(ActivityLogService::class)->log($task->company_id, $user?->id, 'task.status_changed', "Task status changed to {$task->status}: {$task->title}", $task, ['from' => $old, 'to' => $task->status, 'note' => $validated['completion_note'] ?? null]);

        if ($task->status === 'completed') {
            try {
                // notify manager about completion
                $managerUser = $task->manager->user ?? null;
                if ($managerUser) Notification::send($managerUser, new TaskCompleted($task));
            } catch (\Throwable $e) { report($e); }
        }

        return Redirect::back()->with('success','Task status updated.');
    }
}
