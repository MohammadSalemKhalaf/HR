<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();
        });

        $now = now();

        foreach (['admin', 'company', 'manager', 'employee', 'job_seeker'] as $role) {
            DB::table('roles')->insert([
                'id' => (string) Str::uuid(),
                'slug' => $role,
                'name' => str_replace('_', ' ', Str::title($role)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
