<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            // Drop the bigint column and recreate as uuid
            $table->dropColumn('notifiable_id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->uuid('notifiable_id')->after('notifiable_type');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('notifiable_id');
            $table->bigInteger('notifiable_id')->unsigned();
        });
    }
};
