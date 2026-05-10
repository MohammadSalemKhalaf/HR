<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends BaseApiController
{
    private function resolvedCompanyIdForRequest(Request $request): string
    {
        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            return (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
        }

        return (string) $request->string('company_id');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Department::with(['company', 'manager'])->latest();
        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($companyId === '') {
                return $this->error('Validation failed.', ['company_id' => ['Company could not be resolved from token.']], 422);
            }

            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->string('company_id'));
        }

        return $this->success('Departments retrieved successfully.', $query->get());
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $department = Department::with(['company', 'manager'])->find($id);

        if (! $department) {
            return $this->notFound('Department not found.');
        }

        $companyId = $this->resolvedCompanyIdForRequest($request);

        if ($companyId !== '' && $department->company_id !== $companyId) {
            return $this->notFound('Department not found.');
        }

        return $this->success('Department retrieved successfully.', $department);
    }

    public function store(Request $request): JsonResponse
    {
        $actingUser = $request->user();
        $resolvedCompanyId = $request->string('company_id')->toString();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $resolvedCompanyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
        } elseif ($resolvedCompanyId === '') {
            if ($actingUser) {
                $resolvedCompanyId = (string) $this->companyIdForUser($actingUser);
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

        if ($request->filled('manager_employee_id')) {
            $manager = Employee::find($request->string('manager_employee_id'));

            if (! $manager || $manager->company_id !== $resolvedCompanyId) {
                return $this->error('Validation failed.', ['manager_employee_id' => ['Manager must belong to the same company.']], 422);
            }
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

        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($department->company_id !== $companyId) {
                return $this->notFound('Department not found.');
            }
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

    public function update(Request $request, string $id): JsonResponse
    {
        $department = Department::find($id);

        if (! $department) {
            return $this->notFound('Department not found.');
        }

        $companyId = $this->resolvedCompanyIdForRequest($request);

        if ($companyId !== '' && $department->company_id !== $companyId) {
            return $this->notFound('Department not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        if ($request->filled('manager_employee_id')) {
            $manager = Employee::find($request->string('manager_employee_id'));

            if (! $manager || $manager->company_id !== $department->company_id) {
                return $this->error('Validation failed.', ['manager_employee_id' => ['Manager must belong to the same company.']], 422);
            }
        }

        $department->update(array_filter([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'manager_employee_id' => $request->input('manager_employee_id'),
        ], static fn ($value) => $value !== null));

        return $this->success('Department updated successfully.', $department->fresh(['company', 'manager']));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $department = Department::find($id);

        if (! $department) {
            return $this->notFound('Department not found.');
        }

        $companyId = $this->resolvedCompanyIdForRequest($request);

        if ($companyId !== '' && $department->company_id !== $companyId) {
            return $this->notFound('Department not found.');
        }

        $department->delete();

        return $this->success('Department deleted successfully.');
    }
}
