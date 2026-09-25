<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clés étrangères
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // TRUNCATE des tables métier (réinitialise les IDs à 1)
        // ON NE TOUCHE PAS A users, email_verification_codes, etc.
        $tablesToTruncate = [
            'panier_details',
            'commande_details',
            'images_produits',
            'paniers',
            'commandes',
            'produits',
            'categories',
            'bannieres',
            'homepage_images',
            'settings'
        ];

        foreach ($tablesToTruncate as $table) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
        }

        // Réactiver les contraintes de clés étrangères
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Appeler les nouveaux Seeders
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            BannerSeeder::class,
            HomepageSeeder::class,
        ]);
    }
}
