<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DepartmentNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->hasRole('manager')
            && $user->employee instanceof Employee;
    }

    public function rules(): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        $managerEmployee = $user?->employee;
        $companyId = $managerEmployee?->company_id;
        $managerEmployeeId = $managerEmployee?->id;
        $typeKeys = array_keys(config('ems.department_notifications.types', []));

        return [
            'department_id' => [
                'required',
                'uuid',
                Rule::exists('departments', 'id')->where(function ($query) use ($companyId, $managerEmployeeId) {
                    $query->where('company_id', $companyId)
                        ->where('manager_employee_id', $managerEmployeeId);
                }),
            ],
            'recipient_mode' => ['required', Rule::in(['all', 'specific'])],
            'type' => ['required', Rule::in($typeKeys)],
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:3', 'max:5000'],
            'employee_ids' => ['required_if:recipient_mode,specific', 'array', 'min:1'],
            'employee_ids.*' => [
                'uuid',
                Rule::exists('employees', 'id')->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->where('department_id', $this->input('department_id'));
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Please select a department.',
            'recipient_mode.required' => 'Please choose who should receive the notification.',
            'type.required' => 'Please choose a notification type.',
            'title.required' => 'Notification title is required.',
            'message.required' => 'Notification message is required.',
            'employee_ids.min' => 'Select at least one employee when sending to specific employees.',
        ];
    }
}
