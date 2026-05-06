<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Leave::with(['employee.user', 'requestedBy', 'approvedBy'])->latest();

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

        $leave->update([
            'status' => $status,
            'approved_by_user_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return $this->success('Leave status updated successfully.', $leave->fresh(['employee.user', 'requestedBy', 'approvedBy']));
    }
}
