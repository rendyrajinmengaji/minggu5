<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function ($table) {
                $table->string('role')->default('kasir')->after('email');
            });
        }

        DB::table('users')
            ->whereNull('role')
            ->orWhereIn('role', ['', 'user'])
            ->update(['role' => 'kasir']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(255) NOT NULL DEFAULT 'kasir' AFTER `email`");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql' && Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(255) NOT NULL DEFAULT 'user' AFTER `password`");
        }
    }
};