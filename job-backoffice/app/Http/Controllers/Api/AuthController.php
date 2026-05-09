<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends BaseApiController
{
    public function register(Request $request, ApiTokenService $tokens): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'company', 'manager', 'employee', 'job_seeker', 'company_owner'])],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'role_id' => User::roleIdFor($request->string('role')),
        ]);

        return $this->success('User registered successfully.', $this->authPayload($user, $tokens), 201);
    }

    public function login(Request $request, ApiTokenService $tokens): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return $this->error('Invalid credentials.', [], 401);
        }

        return $this->success('Login successful.', $this->authPayload($user, $tokens));
    }

    public function employeeLogin(Request $request, ApiTokenService $tokens): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $user = User::with(['employee.company', 'company'])->where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return $this->error('Invalid credentials.', [], 401);
        }

        if (! $user->hasRole('employee') && ! ($user->roleSlug() === 'job_seeker' && $user->employee)) {
            return $this->error('Not an employee account.', [], 403);
        }

        $employee = $user->employee;

        if (! $employee) {
            return $this->error('Employee profile not found.', [], 404);
        }

        return $this->success('Employee login successful.', [
            'token' => $tokens->issue($user),
            'user' => $user,
            'employee' => $employee,
            'company' => $employee->company,
        ]);
    }

    public function companyLogin(Request $request, ApiTokenService $tokens): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return $this->error('Invalid credentials.', [], 401);
        }

        if (! $user->hasRole(['company', 'manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'Not a company account',
            ], 403);
        }

        // ensure company relation is loaded
        $user->load('company');

        return $this->success('Login successful.', $this->authPayload($user, $tokens));
    }

    public function me(Request $request, ApiTokenService $tokens): JsonResponse
    {
        $user = $request->user();

        return $this->success('Current user retrieved successfully.', $this->authPayload($user, $tokens, true));
    }

    private function authPayload(User $user, ApiTokenService $tokens, bool $includeToken = true): array
    {
        $companyId = $this->companyIdForUser($user);
        $employeeId = $this->employeeIdForUser($user);
        $role = $this->effectiveRole($user);
        $company = null;
        $employee = null;
        if ($companyId) {
            $company = Company::find($companyId);
        }

        if ($employeeId) {
            $employee = Employee::with(['company', 'department', 'manager'])->find($employeeId);
        }

        return [
            'token' => $includeToken ? $tokens->issue($user) : null,
            'user_id' => $user->id,
            'role' => $role,
            'company_id' => $companyId,
            'employee_id' => $employeeId,
            'user' => $user,
            'employee' => $employee,
            'company' => $company,
        ];
    }
}
