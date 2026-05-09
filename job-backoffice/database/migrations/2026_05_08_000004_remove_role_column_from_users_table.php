<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the legacy role column - system now uses role_id exclusively
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore the legacy column in case of rollback
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'company', 'employee', 'job_seeker'])->default('job_seeker')->after('password');
            }
        });
    }
};
