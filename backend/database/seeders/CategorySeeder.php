<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Meubles', 'description' => 'Mobilier d\'intérieur'],
            ['nom' => 'Tables', 'description' => 'Tables à manger et d\'appoint'],
            ['nom' => 'Chaises', 'description' => 'Chaises et tabourets'],
            ['nom' => 'Canapés', 'description' => 'Canapés 2, 3 places et d\'angle'],
            ['nom' => 'Fauteuils', 'description' => 'Fauteuils confortables et modernes'],
            ['nom' => 'Chambres à coucher', 'description' => 'Ensembles pour chambre'],
            ['nom' => 'Armoires', 'description' => 'Rangements et dressing'],
            ['nom' => 'Commodes', 'description' => 'Commodes et meubles d\'appoint'],
            ['nom' => 'Meubles TV', 'description' => 'Meubles de télévision modernes'],
            ['nom' => 'Tables basses', 'description' => 'Tables pour le salon'],
            ['nom' => 'Miroirs', 'description' => 'Miroirs décoratifs'],
            ['nom' => 'Décoration', 'description' => 'Objets de décoration intérieure'],
            ['nom' => 'Éclairage', 'description' => 'Lampes et suspensions'],
            ['nom' => 'Consoles', 'description' => 'Consoles d\'entrée'],
            ['nom' => 'Bibliothèques', 'description' => 'Étagères et bibliothèques'],
            ['nom' => 'Bureaux', 'description' => 'Bureaux pour le télétravail'],
            ['nom' => 'Meubles d\'entrée', 'description' => 'Aménagement d\'entrée']
        ];

        foreach ($categories as $cat) {
            Categorie::firstOrCreate(
                ['nom' => $cat['nom']],
                [
                    'description' => $cat['description'],
                    'actif' => true
                ]
            );
        }
    }
}
