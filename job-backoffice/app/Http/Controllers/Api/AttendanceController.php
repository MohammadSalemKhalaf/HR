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
        $query = AttendanceRecord::with('employee.user')->latest();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->string('employee_id'));
        }

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

    public function checkIn(Request $request, string $employee_id): JsonResponse
    {
        $employee = Employee::find($employee_id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $today = now()->toDateString();

        // Check if already checked in today
        $existing = AttendanceRecord::where('employee_id', $employee_id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in_at) {
            return $this->error('Employee already checked in today.', [], 422);
        }

        $record = $existing ?? new AttendanceRecord([
            'employee_id' => $employee_id,
            'attendance_date' => $today,
        ]);

        $record->check_in_at = now();
        $record->status = 'present';
        $record->save();

        return $this->success('Check-in recorded successfully.', $record->load('employee.user'), 201);
    }

    public function checkOut(Request $request, string $employee_id): JsonResponse
    {
        $employee = Employee::find($employee_id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $today = now()->toDateString();

        // Get today's attendance record
        $record = AttendanceRecord::where('employee_id', $employee_id)
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
}
