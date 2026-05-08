<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees') || ! Schema::hasColumn('employees', 'user_id')) {
            return;
        }

        // Keep only one row per user_id before adding the unique index.
        $duplicateUserIds = DB::table('employees')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        foreach ($duplicateUserIds as $userId) {
            $ids = DB::table('employees')
                ->where('user_id', $userId)
                ->orderBy('created_at')
                ->orderBy('id')
                ->pluck('id')
                ->values();

            if ($ids->count() <= 1) {
                continue;
            }

            DB::table('employees')
                ->whereIn('id', $ids->slice(1)->all())
                ->update([
                    'user_id' => null,
                    'updated_at' => now(),
                ]);
        }

        if (! $this->indexExists('employees', 'employees_user_id_unique')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('employees')) {
            return;
        }

        if ($this->indexExists('employees', 'employees_user_id_unique')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropUnique('employees_user_id_unique');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");

            foreach ($indexes as $idx) {
                if (($idx->name ?? null) === $index) {
                    return true;
                }
            }

            return false;
        }

        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
