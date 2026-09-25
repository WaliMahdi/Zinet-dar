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
        // 1. Convert existing records
        DB::table('commandes')->where('statut', 'en_preparation')->update(['statut' => 'confirmee']);
        DB::table('commandes')->where('statut', 'expediee')->update(['statut' => 'livree']);

        // 2. Modify ENUM column
        DB::statement("ALTER TABLE commandes MODIFY statut ENUM('en_attente', 'confirmee', 'livree', 'annulee') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the removed statuses in case of rollback
        DB::statement("ALTER TABLE commandes MODIFY statut ENUM('en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente'");
    }
};
