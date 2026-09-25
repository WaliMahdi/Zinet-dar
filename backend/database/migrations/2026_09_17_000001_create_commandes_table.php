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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('produit_id')
                ->constrained('produits')
                ->restrictOnDelete();

            $table->string('nom_client');
            $table->string('telephone');
            $table->text('adresse');

            $table->unsignedInteger('quantite')->default(1);

            $table->decimal('prix_unitaire', 10, 3);
            $table->decimal('montant_total', 10, 3);

            $table->enum('mode_paiement', [
                'espece',
            ])->default('espece');

            $table->enum('statut', [
                'en_attente',
                'confirmee',
                'en_preparation',
                'expediee',
                'livree',
                'annulee',
            ])->default('en_attente');

            $table->text('note_admin')->nullable();
            $table->timestamp('date_traitement')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
