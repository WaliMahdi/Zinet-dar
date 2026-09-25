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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sous_categorie_id')->nullable()->constrained('sous_categories')->onDelete('set null');
            
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('reference')->nullable();
            $table->string('marque')->nullable();
            $table->text('description')->nullable();
            
            $table->decimal('prix', 10, 2);
            $table->enum('type_remise', ['aucune', 'pourcentage', 'fixe'])->default('aucune');
            $table->decimal('valeur_remise', 10, 2)->nullable();
            $table->decimal('prix_apres_remise', 10, 2);
            $table->date('date_debut_remise')->nullable();
            $table->date('date_fin_remise')->nullable();
            
            $table->unsignedInteger('quantite_stock')->default(0);
            
            $table->boolean('actif')->default(true);
            $table->boolean('vedette')->default(false);
            $table->boolean('nouveau')->default(false);
            $table->boolean('en_promotion')->default(false);
            
            $table->integer('ordre')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
