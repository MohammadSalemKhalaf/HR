<?php

namespace App\Http\Controllers\Api;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $authorization = $this->employeeAuthorization($request);
        if ($authorization instanceof JsonResponse) {
            return $authorization;
        }

        $employee = $authorization;
        $query = AttendanceRecord::with('employee.user')->latest();

        $query->where('employee_id', $employee->id);

        if ($request->filled('attendance_date')) {
            $query->where('attendance_date', $request->date('attendance_date'));
        }

        if ($request->filled('from_date')) {
            $query->where('attendance_date', '>=', $request->date('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->where('attendance_date', '<=', $request->date('to_date'));
        }

        return $this->success('Attendance records retrieved successfully.', $query->get());
    }

    public function checkIn(Request $request, ?string $employee_id = null): JsonResponse
    {
        $authorization = $this->employeeAuthorization($request, $employee_id);
        if ($authorization instanceof JsonResponse) {
            return $authorization;
        }

        $employee = $authorization;
        $today = now()->toDateString();

        $existing = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in_at) {
            return $this->error('Employee already checked in today.', [], 422);
        }

        $record = $existing ?? new AttendanceRecord([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
        ]);

        $record->check_in_at = now();
        $record->status = 'present';
        $record->save();

        return $this->success('Check-in recorded successfully.', $record->load('employee.user'), 201);
    }

    public function checkOut(Request $request, ?string $employee_id = null): JsonResponse
    {
        $authorization = $this->employeeAuthorization($request, $employee_id);
        if ($authorization instanceof JsonResponse) {
            return $authorization;
        }

        $employee = $authorization;
        $today = now()->toDateString();

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if (! $record) {
            return $this->error('No check-in found for today. Please check in first.', [], 422);
        }

        if ($record->check_out_at) {
            return $this->error('Employee already checked out today.', [], 422);
        }

        $record->check_out_at = now();
        $record->save();

        return $this->success('Check-out recorded successfully.', $record->fresh('employee.user'));
    }

    private function employeeAuthorization(Request $request, ?string $employeeId = null): Employee|JsonResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'employee') {
            return $this->error('Forbidden.', [], 403);
        }

        $employee = $user->employee;

        if (! $employee) {
            return $this->notFound('Employee profile not found.');
        }

        if ($employeeId && $employee->id !== $employeeId) {
            return $this->error('Forbidden.', [], 403);
        }

        return $employee;
    }
}
