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
        Schema::dropIfExists('demandes_facilite');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not recreating the complex table since it's permanently removed
        // If really needed, it would recreate the old structure.
        // We leave it empty or minimal to prevent issues.
    }
};
