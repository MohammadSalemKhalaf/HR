<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignUuid('role_id')->nullable()->after('email_verified_at')->constrained('roles')->nullOnDelete();
            });
        }

        $roleIds = DB::table('roles')->pluck('id', 'slug');

        $legacyRoleMap = [
            'admin' => $roleIds['admin'] ?? null,
            'company' => $roleIds['company'] ?? null,
            'company_owner' => $roleIds['company'] ?? null,
            'manager' => $roleIds['manager'] ?? null,
            'employee' => $roleIds['employee'] ?? null,
            'job_seeker' => $roleIds['job_seeker'] ?? null,
        ];

        foreach ($legacyRoleMap as $legacyRole => $roleId) {
            if ($roleId === null) {
                continue;
            }

            DB::table('users')
                ->where('role', $legacyRole)
                ->update(['role_id' => $roleId]);
        }

        if (! Schema::hasTable('companies') || ! Schema::hasTable('employees')) {
            return;
        }

        $users = DB::table('users')->select('id', 'role_id', 'role')->get();

        foreach ($users as $user) {
            $roleSlug = null;

            if (DB::table('companies')->where('ownerId', $user->id)->exists()) {
                $roleSlug = 'company';
            } else {
                $employee = DB::table('employees')->where('user_id', $user->id)->first();

                if ($employee) {
                    $departmentManagerId = null;

                    if (! empty($employee->department_id)) {
                        $departmentManagerId = DB::table('departments')
                            ->where('id', $employee->department_id)
                            ->value('manager_employee_id');
                    }

                    if ($departmentManagerId === $employee->id) {
                        $roleSlug = 'manager';
                    } elseif (str_contains(strtolower((string) ($employee->job_title ?? '')), 'manager')) {
                        $roleSlug = 'manager';
                    } else {
                        $roleSlug = 'employee';
                    }
                } else {
                    $roleSlug = 'job_seeker';
                }
            }

            $roleId = $legacyRoleMap[$roleSlug] ?? null;

            if ($roleId && (
                ! $user->role_id ||
                $roleSlug === 'company' ||
                $roleSlug === 'manager' ||
                $roleSlug === 'employee'
            )) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role_id' => $roleId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
