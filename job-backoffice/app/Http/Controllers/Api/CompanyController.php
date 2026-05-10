<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            } elseif ($request->filled('email') && $request->filled('password')) {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
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
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $company->update(array_filter([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'industry' => $request->input('industry'),
            'website' => $request->input('website'),
            'ownerId' => $request->input('owner_id'),
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
