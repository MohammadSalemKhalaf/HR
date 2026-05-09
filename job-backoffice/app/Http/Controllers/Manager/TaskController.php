<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\EmployeeTask;
use App\Models\Employee;
use App\Models\Department;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskCompleted;

class TaskController extends Controller
{
    private function managerEmployeeId()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->employee?->id;
    }

    private function managedDepartmentIds(): array
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) return [];
        return Department::where('manager_employee_id', $managerId)->pluck('id')->map(fn($v)=> (string)$v)->toArray();
    }

    public function index()
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $tasks = EmployeeTask::where('manager_employee_id', $managerId)
            ->with(['employee.user','department'])
            ->latest()
            ->paginate(15);

        return view('manager.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $deptIds = $this->managedDepartmentIds();
        $employees = Employee::whereIn('department_id', $deptIds)->with('user')->get();

        return view('manager.tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $deptIds = $this->managedDepartmentIds();

        $validated = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'repository_url' => ['nullable','url'],
            'priority' => ['required','in:low,medium,high'],
            'employee_id' => ['required','exists:employees,id'],
            'due_date' => ['nullable','date'],
        ]);

        $employee = Employee::find($validated['employee_id']);
        if (!$employee || !in_array($employee->department_id, $deptIds)) {
            abort(403);
        }

        $task = EmployeeTask::create([
            'company_id' => $employee->company_id,
            'department_id' => $employee->department_id,
            'manager_employee_id' => $managerId,
            'employee_id' => $employee->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'repository_url' => $validated['repository_url'] ?? null,
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        app(ActivityLogService::class)->log($task->company_id, $user?->id, 'task.created', "Task created: {$task->title}", $task, ['employee_id' => $task->employee_id]);

        // create in-app notification and attempt email (must not break request)
        try {
            /** @var \App\Models\User|null $recipient */
            $recipient = $task->employee->user ?? null;
            if ($recipient) {
                Notification::send($recipient, new TaskAssigned($task));
            }
        } catch (\Throwable $e) {
            // swallow mail/notification errors to avoid breaking flow
            report($e);
        }

        return Redirect::route('manager.tasks.index')->with('success','Task created.');
    }

    public function show(string $id)
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $task = EmployeeTask::with(['employee.user','department'])->findOrFail($id);
        if ($task->manager_employee_id !== $managerId) abort(403);

        return view('manager.tasks.show', compact('task'));
    }

    public function edit(string $id)
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $task = EmployeeTask::findOrFail($id);
        if ($task->manager_employee_id !== $managerId) abort(403);

        $deptIds = $this->managedDepartmentIds();
        $employees = Employee::whereIn('department_id', $deptIds)->with('user')->get();

        return view('manager.tasks.edit', compact('task','employees'));
    }

    public function update(Request $request, string $id)
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $task = EmployeeTask::findOrFail($id);
        if ($task->manager_employee_id !== $managerId) abort(403);

        $deptIds = $this->managedDepartmentIds();

        $validated = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'repository_url' => ['nullable','url'],
            'priority' => ['required','in:low,medium,high'],
            'employee_id' => ['required','exists:employees,id'],
            'status' => ['required','in:pending,in_progress,completed'],
            'due_date' => ['nullable','date'],
        ]);

        $employee = Employee::find($validated['employee_id']);
        if (!$employee || !in_array($employee->department_id, $deptIds)) abort(403);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'repository_url' => $validated['repository_url'] ?? null,
            'priority' => $validated['priority'],
            'employee_id' => $employee->id,
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        app(ActivityLogService::class)->log($task->company_id, $user?->id, 'task.updated', "Task updated: {$task->title}", $task, ['status' => $task->status]);

        // if status changed to completed, notify manager
        if ($task->status === 'completed') {
            try {
                /** @var \App\Models\User|null $managerUser */
                $managerUser = $task->manager->user ?? null;
                if ($managerUser) Notification::send($managerUser, new TaskCompleted($task));
            } catch (\Throwable $e) { report($e); }
        }

        return Redirect::route('manager.tasks.show', $task->id)->with('success','Task updated.');
    }

    public function destroy(string $id)
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) abort(403);

        $task = EmployeeTask::findOrFail($id);
        if ($task->manager_employee_id !== $managerId) abort(403);

        $task->delete();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        app(ActivityLogService::class)->log($task->company_id, $user?->id, 'task.deleted', "Task deleted: {$task->title}", $task);

        return Redirect::route('manager.tasks.index')->with('success','Task deleted.');
    }
}
