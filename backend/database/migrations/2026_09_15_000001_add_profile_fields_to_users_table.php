<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds profile fields (username, first_name, last_name) to the users table.
     * The legacy `name` column is kept but made nullable so existing data is not lost.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make the legacy name column nullable (preserve, don't drop)
            $table->string('name')->nullable()->change();

            // New profile fields
            $table->string('username', 191)->nullable()->unique()->after('name');
            $table->string('first_name', 191)->nullable()->after('username');
            $table->string('last_name', 191)->nullable()->after('first_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'first_name', 'last_name']);

            // Restore name to not-nullable
            $table->string('name')->nullable(false)->change();
        });
    }
};
