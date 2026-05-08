<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class BaseApiController extends Controller
{
    protected function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(string $message, mixed $errors = [], int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->error($message, [], 404);
    }

    protected function effectiveRole(User $user): string
    {
        $role = $user->roleSlug();

        if ($role && $role !== 'job_seeker') {
            return $role;
        }

        if (! Schema::hasTable('employees')) {
            return $role ?? 'job_seeker';
        }

        if (Employee::where('user_id', $user->id)->exists()) {
            return 'employee';
        }

        return $role ?? 'job_seeker';
    }

    protected function companyIdForUser(User $user): ?string
    {
        if (Schema::hasTable('employees')) {
            $employeeCompany = Employee::where('user_id', $user->id)->value('company_id');

            if ($employeeCompany) {
                return $employeeCompany;
            }
        }

        if (Schema::hasTable('companies')) {
            return Company::where('ownerId', $user->id)->value('id');
        }

        return null;
    }

    protected function employeeIdForUser(User $user): ?string
    {
        if (! Schema::hasTable('employees')) {
            return null;
        }

        return Employee::where('user_id', $user->id)->value('id');
    }
}
