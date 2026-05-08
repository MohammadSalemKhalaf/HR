<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable()->index();
            $table->uuid('department_id')->nullable()->index();
            $table->uuid('manager_employee_id')->nullable()->index();
            $table->uuid('employee_id')->nullable()->index();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('repository_url')->nullable();

            $table->enum('priority', ['low','medium','high'])->default('medium');
            $table->enum('status', ['pending','in_progress','completed'])->default('pending');

            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_tasks');
    }
};
