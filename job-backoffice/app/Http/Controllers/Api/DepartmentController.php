<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Department::with(['company', 'manager'])->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->string('company_id'));
        }

        return $this->success('Departments retrieved successfully.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $resolvedCompanyId = $request->string('company_id')->toString();

        if ($resolvedCompanyId === '') {
            $user = $request->user();
            if ($user) {
                $resolvedCompanyId = (string) $this->companyIdForUser($user);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        if ($resolvedCompanyId === '') {
            return $this->error('Validation failed.', ['company_id' => ['Company could not be resolved from token.']], 422);
        }

        $department = Department::create([
            'company_id' => $resolvedCompanyId,
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'manager_employee_id' => $request->input('manager_employee_id'),
        ]);

        return $this->success('Department created successfully.', $department->load(['company', 'manager']), 201);
    }

    public function assignManager(Request $request, string $id): JsonResponse
    {
        $department = Department::find($id);

        if (! $department) {
            return $this->notFound('Department not found.');
        }

        $validator = Validator::make($request->all(), [
            'manager_employee_id' => ['required', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $manager = Employee::find($request->string('manager_employee_id'));

        if (! $manager || $manager->company_id !== $department->company_id) {
            return $this->error('Manager must belong to the same company.', [], 422);
        }

        $department->update([
            'manager_employee_id' => $manager->id,
        ]);

        return $this->success('Department manager assigned successfully.', $department->fresh(['company', 'manager']));
    }
}
