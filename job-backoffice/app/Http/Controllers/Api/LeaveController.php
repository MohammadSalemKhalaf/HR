<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\NotificationPreference;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRequested;
use App\Notifications\LeaveRejected;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LeaveController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Leave::with(['employee.user', 'requestedBy', 'approvedBy'])->latest();

        $employee = Employee::where('user_id', $request->user()->id)->first();

        if ($employee) {
            $query->where('employee_id', $employee->id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->string('employee_id'));
        }

        return $this->success('Leaves retrieved successfully.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type' => ['required', 'in:annual,sick,unpaid,other'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days_count' => ['nullable', 'integer', 'min:1'],
            'requested_by_user_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $employee = Employee::find($request->string('employee_id'));
        $daysCount = $request->input('days_count');

        if (! $daysCount) {
            $daysCount = $request->date('start_date')->diffInDays($request->date('end_date')) + 1;
        }

        $leave = Leave::create([
            'employee_id' => $employee->id,
            'leave_type' => $request->string('leave_type'),
            'start_date' => $request->date('start_date'),
            'end_date' => $request->date('end_date'),
            'days_count' => $daysCount,
            'status' => 'pending',
            'requested_by_user_id' => $request->input('requested_by_user_id', $request->user()->id),
            'approved_by_user_id' => null,
            'approved_at' => null,
        ]);

        try {
            $managerUser = $employee->manager?->user;

            if ($managerUser && $this->wantsEmail($managerUser, 'leave_requested')) {
                Notification::send($managerUser, new LeaveRequested($leave->load(['employee.user'])));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->success('Leave request created successfully.', $leave->load(['employee.user', 'requestedBy', 'approvedBy']), 201);
    }

    public function approve(Request $request): JsonResponse
    {
        return $this->setDecision($request, 'approved');
    }

    public function reject(Request $request): JsonResponse
    {
        return $this->setDecision($request, 'rejected');
    }

    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'leave_id' => ['required', 'exists:leaves,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $leave = Leave::find($request->string('leave_id'));

        if (! $leave) {
            return $this->notFound('Leave not found.');
        }

        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (! $employee || $leave->employee_id !== $employee->id) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return $this->error('Only pending leave requests can be canceled.', null, 422);
        }

        $leaveId = $leave->id;
        $leave->delete();

        return $this->success('Leave request canceled successfully.', ['leave_id' => $leaveId]);
    }

    private function setDecision(Request $request, string $status): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'leave_id' => ['required', 'exists:leaves,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $leave = Leave::find($request->string('leave_id'));

        if (! $leave) {
            return $this->notFound('Leave not found.');
        }

        $user = Auth::user();
        $manager = $user ? Employee::where('user_id', $user->id)->first() : null;

        if (! $manager) {
            abort(403);
        }

        $deptIds = Department::where('manager_employee_id', $manager->id)->pluck('id');
        $employeeIds = Employee::whereIn('department_id', $deptIds)->pluck('id');

        if (! in_array($leave->employee_id, $employeeIds->toArray())) {
            abort(403);
        }

        $leave->update([
            'status' => $status,
            'approved_by_user_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        app(ActivityLogService::class)->log(
            $manager->company_id ?? null,
            $request->user()->id,
            "leave.{$status}",
            ucfirst($status) . " leave for {$leave->employee?->user?->name}",
            $leave,
            ['leave_id' => $leave->id]
        );

        try {
            if ($leave->employee?->user) {
                $notification = $status === 'approved'
                    ? new LeaveApproved($leave)
                    : new LeaveRejected($leave);

                Notification::send($leave->employee->user, $notification);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->success('Leave status updated successfully.', $leave->fresh(['employee.user', 'requestedBy', 'approvedBy']));
    }

    private function wantsEmail($user, string $type): bool
    {
        $preferences = NotificationPreference::forUser($user);

        return $preferences?->wantsEmail($type) ?? true;
    }
}
