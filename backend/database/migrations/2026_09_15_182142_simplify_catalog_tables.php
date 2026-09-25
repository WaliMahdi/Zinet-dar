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
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'ordre']);
        });

        Schema::table('sous_categories', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'ordre']);
        });

        Schema::table('produits', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug', 
                'type_remise', 
                'valeur_remise', 
                'date_debut_remise', 
                'date_fin_remise', 
                'en_promotion', 
                'ordre'
            ]);

            $table->text('caracteristiques')->nullable();
            $table->decimal('remise', 5, 2)->default(0);
            $table->string('garantie')->nullable();
            $table->string('image')->nullable();
            
            // Change prix and prix_apres_remise to 10, 3 precision
            $table->decimal('prix', 10, 3)->change();
            $table->decimal('prix_apres_remise', 10, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic skipped for brevity, since this is a forward-only structural update
    }
};
