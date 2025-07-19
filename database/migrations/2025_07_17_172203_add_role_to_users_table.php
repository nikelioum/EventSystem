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
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            // Add index on email if not already present
            if (!Schema::hasIndex('users', 'users_email_index')) {
                $table->index('email', 'users_email_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop role column only if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            // Drop email index only if it exists
            if (Schema::hasIndex('users', 'users_email_index')) {
                $table->dropIndex('users_email_index');
            }
        });
    }
};
