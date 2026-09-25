<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commande_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commande_id')
                ->constrained('commandes')
                ->cascadeOnDelete();

            $table->foreignId('produit_id')
                ->constrained('produits')
                ->restrictOnDelete();

            $table->string('nom_produit');
            $table->string('reference_produit')->nullable();

            $table->decimal('prix_unitaire', 10, 3);
            $table->unsignedInteger('quantite');
            $table->decimal('montant', 10, 3);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_details');
    }
};
