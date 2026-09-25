<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // Drop foreign key before dropping the column
            $table->dropForeign(['produit_id']);
            
            $table->dropColumn([
                'produit_id',
                'quantite',
                'prix_unitaire',
            ]);

            $table->decimal('sous_total', 10, 3)->after('adresse');
            $table->text('note_client')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreignId('produit_id')->nullable()->constrained('produits')->restrictOnDelete();
            $table->unsignedInteger('quantite')->default(1);
            $table->decimal('prix_unitaire', 10, 3)->nullable();

            $table->dropColumn(['sous_total', 'note_client']);
        });
    }
};
