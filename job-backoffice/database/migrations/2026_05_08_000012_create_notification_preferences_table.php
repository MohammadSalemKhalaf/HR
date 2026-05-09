<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('email_task_assigned')->default(true);
            $table->boolean('email_task_completed')->default(true);
            $table->boolean('email_leave_requested')->default(true);
            $table->boolean('email_leave_approval')->default(true);
            $table->boolean('email_weekly_report')->default(true);
            $table->boolean('email_activity_digest')->default(false);
            $table->boolean('in_app_notifications')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
