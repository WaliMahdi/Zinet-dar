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
            $table->dropColumn(['title', 'description', 'button_text', 'button_link']);
            $table->enum('section', ['accueil', 'boutique', 'nouveautes', 'promotions'])->default('accueil')->after('image_public_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bannieres', function (Blueprint $table) {
            $table->dropColumn('section');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
        });
    }
};
