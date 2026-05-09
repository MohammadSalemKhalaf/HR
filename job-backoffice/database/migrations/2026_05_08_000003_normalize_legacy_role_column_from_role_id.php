<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        $allowed = ['admin', 'company', 'manager', 'employee', 'job_seeker'];
        $roleSlugsById = Schema::hasTable('roles')
            ? DB::table('roles')->pluck('slug', 'id')
            : collect();

        $users = DB::table('users')->select('id', 'role_id', 'role')->get();

        foreach ($users as $user) {
            $normalized = null;

            if (! empty($user->role_id) && isset($roleSlugsById[$user->role_id])) {
                $normalized = (string) $roleSlugsById[$user->role_id];
            } else {
                $normalized = (string) ($user->role ?? '');
            }

            if ($normalized === 'company_owner') {
                $normalized = 'company';
            }

            // Skip if normalized value is not in allowed list
            if (! in_array($normalized, $allowed, true)) {
                $normalized = 'job_seeker';
            }

            // Only update if different and valid
            if (($user->role ?? null) !== $normalized && in_array($normalized, $allowed, true)) {
                try {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'role' => $normalized,
                            'updated_at' => now(),
                        ]);
                } catch (\Exception $e) {
                    // Log and continue if specific user update fails
                    \Log::warning("Failed to update role for user {$user->id}: " . $e->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        // No-op: this migration normalizes legacy role strings to align with role_id.
    }
};
