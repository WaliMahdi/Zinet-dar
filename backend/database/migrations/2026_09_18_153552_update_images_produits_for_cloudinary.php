<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('images_produits', function (Blueprint $table) {
            $table->renameColumn('chemin', 'url');
            $table->renameColumn('texte_alternatif', 'alt_text');
            $table->renameColumn('principale', 'is_principale');
            $table->string('public_id')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('images_produits', function (Blueprint $table) {
            $table->dropColumn('public_id');
            $table->renameColumn('url', 'chemin');
            $table->renameColumn('alt_text', 'texte_alternatif');
            $table->renameColumn('is_principale', 'principale');
        });
    }
};

