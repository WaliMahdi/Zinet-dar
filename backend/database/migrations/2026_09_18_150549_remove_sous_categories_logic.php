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
        // 1. Remove the foreign key and column from produits
        Schema::table('produits', function (Blueprint $table) {
            if (Schema::hasColumn('produits', 'sous_categorie_id')) {
                // Drop the foreign key constraint first. The default naming convention is table_column_foreign.
                $table->dropForeign(['sous_categorie_id']);
                $table->dropColumn('sous_categorie_id');
            }
        });

        // 2. Drop the sous_categories table
        Schema::dropIfExists('sous_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the sous_categories table
        Schema::create('sous_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->string('nom');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Re-add the column and foreign key to produits
        Schema::table('produits', function (Blueprint $table) {
            $table->foreignId('sous_categorie_id')->nullable()->constrained('sous_categories')->onDelete('set null');
        });
    }
};
