<?php

namespace App\Http\Controllers\Api;

use App\Models\EmployeeTask;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Notifications\TaskCompleted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EmployeeTaskController extends BaseApiController
{
    /**
     * Get tasks assigned to the current employee
     */
    public function index(): JsonResponse
    {
        try {
            $user = request()->user();
            $employee = $user->employee;

            if (!$employee) {
                return $this->error('Employee profile not found.', [], 404);
            }

            $tasks = EmployeeTask::where('employee_id', $employee->id)
                ->with(['manager.user', 'department', 'company'])
                ->orderBy('due_date', 'asc')
                ->get();

            return $this->success('Tasks retrieved successfully.', $tasks);
        } catch (\Throwable $e) {
            report($e);
            return $this->error('Failed to retrieve tasks.', [], 500);
        }
    }

    /**
     * Get a specific task assigned to the employee
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = request()->user();
            $employee = $user->employee;

            if (!$employee) {
                return $this->error('Employee profile not found.', [], 404);
            }

            $task = EmployeeTask::where('employee_id', $employee->id)
                ->where('id', $id)
                ->with(['manager.user', 'department', 'company'])
                ->first();

            if (!$task) {
                return $this->error('Task not found.', [], 404);
            }

            return $this->success('Task retrieved successfully.', $task);
        } catch (\Throwable $e) {
            report($e);
            return $this->error('Failed to retrieve task.', [], 500);
        }
    }

    /**
     * Update task status (employee can only change status)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = request()->user();
            $employee = $user->employee;

            if (!$employee) {
                return $this->error('Employee profile not found.', [], 404);
            }

            $task = EmployeeTask::where('employee_id', $employee->id)
                ->where('id', $id)
                ->first();

            if (!$task) {
                return $this->error('Task not found.', [], 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
            ]);

            $task->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

            if ($validated['status'] === 'completed') {
                $managerUser = $task->manager?->user;

                if ($managerUser && $this->wantsEmail($managerUser, 'task_completed')) {
                    try {
                        Notification::send($managerUser, new TaskCompleted($task->fresh(['manager.user', 'employee.user'])));
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }

            return $this->success('Task updated successfully.', $task);
        } catch (\Throwable $e) {
            report($e);
            return $this->error('Failed to update task.', [], 500);
        }
    }

    private function wantsEmail(mixed $user, string $type): bool
    {
        if (! $user instanceof User) {
            return true;
        }

        $preferences = NotificationPreference::forUser($user);

        return $preferences?->wantsEmail($type) ?? true;
    }
}
