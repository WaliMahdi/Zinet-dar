<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panier_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('panier_id')
                ->constrained('paniers')
                ->cascadeOnDelete();

            $table->foreignId('produit_id')
                ->constrained('produits')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantite');

            $table->timestamps();

            // A product can only appear once in a given cart
            $table->unique(['panier_id', 'produit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panier_details');
    }
};
