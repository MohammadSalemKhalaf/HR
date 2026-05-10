<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CompanyController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Company::with('owner')->latest();

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        $companies = $query->paginate(10);

        return $this->success('Companies retrieved successfully.', $companies);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('companies', 'name')],
            'address' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', Rule::unique('companies', 'website')],
            'owner_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $company = DB::transaction(function () use ($request) {
            $companyRoleId = User::roleIdFor('company');
            $user = null;

            if ($request->filled('owner_id')) {
                $user = User::findOrFail($request->input('owner_id'));
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'role_id' => $companyRoleId,
                    ]);
            } elseif ($request->filled('email')) {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password') ?: Str::random(12)),
                ]);

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'role_id' => $companyRoleId,
                    ]);
            } else {
                $user = $request->user();
            }

            return Company::create([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'industry' => $request->input('industry'),
                'website' => $request->input('website'),
                'ownerId' => $user->id,
            ]);
        });

        return $this->success('Company created successfully.', $company->load('owner'), 201);
    }

    public function myCompany(Request $request): JsonResponse
    {
        $actingUser = $request->user();
        $companyId = (string) ($actingUser?->company?->id ?? $actingUser?->employee?->company_id ?? '');

        if ($companyId === '') {
            return $this->notFound('Company not found.');
        }

        $company = Company::with('owner')->find($companyId);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        return $this->success('Company retrieved successfully.', $company);
    }

    public function updateMyCompany(Request $request): JsonResponse
    {
        $actingUser = $request->user();
        $companyId = (string) ($actingUser?->company?->id ?? $actingUser?->employee?->company_id ?? '');

        if ($companyId === '') {
            return $this->notFound('Company not found.');
        }

        return $this->update($request, $companyId);
    }

    public function companyDashboardStats(Request $request): JsonResponse
    {
        $actingUser = $request->user();
        $companyId = (string) ($actingUser?->company?->id ?? $actingUser?->employee?->company_id ?? '');

        if ($companyId === '') {
            return $this->error('Validation failed.', ['company_id' => ['Company could not be resolved from token.']], 422);
        }

        $jobsCount = \App\Models\JobVacancy::where('companyId', $companyId)->whereNull('deleted_at')->count();
        $applicationsCount = \App\Models\JobApplication::whereHas('jobvacancy', function ($query) use ($companyId) {
            $query->where('companyId', $companyId);
        })->whereNull('deleted_at')->count();
        $departmentsCount = \App\Models\Department::where('company_id', $companyId)->count();
        $employeesCount = \App\Models\Employee::where('company_id', $companyId)->whereNull('deleted_at')->count();

        return $this->success('Company dashboard stats retrieved successfully.', [
            'jobs' => $jobsCount,
            'applications' => $applicationsCount,
            'departments' => $departmentsCount,
            'employees' => $employeesCount,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $company = Company::find($id);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        $company = Company::find($id);

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($company?->id)],
            'address' => ['sometimes', 'string', 'max:255'],
            'industry' => ['sometimes', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', Rule::unique('companies', 'website')->ignore($company?->id)],
            'owner_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        // If an email was provided, ensure there's a user for that email and assign as owner
        $ownerId = $request->input('owner_id');

        if ($request->filled('email')) {
            $companyRoleId = User::roleIdFor('company');

            $user = User::where('email', $request->input('email'))->first();

            if (! $user) {
                $user = User::create([
                    'name' => $request->input('name') ?: $company->name,
                    'email' => $request->input('email'),
                    'password' => Hash::make(Str::random(12)),
                ]);

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'role_id' => $companyRoleId,
                    ]);
            } else {
                // ensure role is set to company role
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'role_id' => $companyRoleId,
                    ]);
            }

            $ownerId = $user->id;
        }

        $company->update(array_filter([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'industry' => $request->input('industry'),
            'website' => $request->input('website'),
            'ownerId' => $ownerId,
        ], static fn ($value) => $value !== null));

        return $this->success('Company updated successfully.', $company->fresh('owner'));
    }

    public function show(string $id): JsonResponse
    {
        $company = Company::with('owner')->find($id);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        return $this->success('Company retrieved successfully.', $company);
    }

    public function destroy(string $id): JsonResponse
    {
        $company = Company::find($id);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        $company->delete();

        return $this->success('Company archived successfully.');
    }

    public function restore(string $id): JsonResponse
    {
        $company = Company::withTrashed()->find($id);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        if (! $company->trashed()) {
            return $this->error('Company is not archived.', [], 422);
        }

        $company->restore();

        return $this->success('Company restored successfully.', $company->load('owner'));
    }
}
