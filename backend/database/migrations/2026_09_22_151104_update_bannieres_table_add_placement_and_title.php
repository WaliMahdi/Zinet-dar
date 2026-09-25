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
        Schema::table('bannieres', function (Blueprint $table) {
            $table->string('title')->nullable()->after('image_public_id');
        });
        
        // Mettre à jour l'ENUM pour inclure "categories"
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE bannieres MODIFY COLUMN section ENUM('accueil', 'boutique', 'nouveautes', 'promotions', 'categories') DEFAULT 'accueil'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bannieres', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE bannieres MODIFY COLUMN section ENUM('accueil', 'boutique', 'nouveautes', 'promotions') DEFAULT 'accueil'");
    }
};
