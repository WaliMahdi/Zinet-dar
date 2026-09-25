<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Guards each column drop with a hasColumn check so the migration
     * is idempotent and safe on both:
     *   - A fresh database that never had the facilite columns.
     *   - A MySQL database that had the old facilite migration applied.
     */
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $toDrop = array_filter(
                ['taux_facilite', 'nombre_mois_facilite', 'total_facilite', 'mensualite_facilite'],
                fn (string $col) => Schema::hasColumn('produits', $col)
            );

            if (! empty($toDrop)) {
                $table->dropColumn(array_values($toDrop));
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            if (! Schema::hasColumn('produits', 'taux_facilite')) {
                $table->decimal('taux_facilite', 5, 2)->nullable();
            }
            if (! Schema::hasColumn('produits', 'nombre_mois_facilite')) {
                $table->unsignedInteger('nombre_mois_facilite')->default(18);
            }
            if (! Schema::hasColumn('produits', 'total_facilite')) {
                $table->decimal('total_facilite', 10, 3)->nullable();
            }
            if (! Schema::hasColumn('produits', 'mensualite_facilite')) {
                $table->decimal('mensualite_facilite', 10, 3)->nullable();
            }
        });
    }
};
