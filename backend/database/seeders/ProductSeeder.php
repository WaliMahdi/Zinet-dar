<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\Categorie;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // MEUBLES
            ['nom' => 'Buffet moderne 3 portes', 'cat' => 'Meubles', 'prix' => 1200, 'promo' => null, 'stock' => 5],
            ['nom' => 'Buffet en bois naturel', 'cat' => 'Meubles', 'prix' => 1450, 'promo' => null, 'stock' => 2],
            ['nom' => 'Meuble de rangement moderne', 'cat' => 'Meubles', 'prix' => 850, 'promo' => 750, 'stock' => 15],
            ['nom' => 'Meuble d\'entrée contemporain', 'cat' => 'Meubles', 'prix' => 900, 'promo' => null, 'stock' => 0],
            
            // TABLES
            ['nom' => 'Table à manger moderne', 'cat' => 'Tables', 'prix' => 1500, 'promo' => null, 'stock' => 10],
            ['nom' => 'Table à manger extensible', 'cat' => 'Tables', 'prix' => 1800, 'promo' => 1650, 'stock' => 3],
            ['nom' => 'Table ronde en bois', 'cat' => 'Tables', 'prix' => 1250, 'promo' => null, 'stock' => 8],
            ['nom' => 'Table rectangulaire moderne', 'cat' => 'Tables', 'prix' => 1400, 'promo' => null, 'stock' => 4],
            ['nom' => 'Table basse moderne', 'cat' => 'Tables basses', 'prix' => 450, 'promo' => 390, 'stock' => 12],
            ['nom' => 'Table basse en bois', 'cat' => 'Tables basses', 'prix' => 550, 'promo' => null, 'stock' => 0],
            ['nom' => 'Table d\'appoint', 'cat' => 'Tables basses', 'prix' => 250, 'promo' => null, 'stock' => 20],

            // CHAISES
            ['nom' => 'Chaise scandinave', 'cat' => 'Chaises', 'prix' => 150, 'promo' => null, 'stock' => 40],
            ['nom' => 'Chaise moderne', 'cat' => 'Chaises', 'prix' => 220, 'promo' => 180, 'stock' => 24],
            ['nom' => 'Chaise en bois massif', 'cat' => 'Chaises', 'prix' => 280, 'promo' => null, 'stock' => 12],
            ['nom' => 'Chaise rembourrée premium', 'cat' => 'Chaises', 'prix' => 350, 'promo' => null, 'stock' => 8],

            // CANAPÉS
            ['nom' => 'Canapé 3 places velours', 'cat' => 'Canapés', 'prix' => 2200, 'promo' => 1950, 'stock' => 5],
            ['nom' => 'Canapé 4 places familial', 'cat' => 'Canapés', 'prix' => 2800, 'promo' => null, 'stock' => 2],
            ['nom' => 'Canapé d\'angle modulable', 'cat' => 'Canapés', 'prix' => 3500, 'promo' => 3100, 'stock' => 3],
            ['nom' => 'Canapé convertible pratique', 'cat' => 'Canapés', 'prix' => 1900, 'promo' => null, 'stock' => 0],

            // FAUTEUILS
            ['nom' => 'Fauteuil moderne', 'cat' => 'Fauteuils', 'prix' => 650, 'promo' => null, 'stock' => 7],
            ['nom' => 'Fauteuil lounge relax', 'cat' => 'Fauteuils', 'prix' => 890, 'promo' => 790, 'stock' => 4],
            ['nom' => 'Fauteuil en tissu bouclette', 'cat' => 'Fauteuils', 'prix' => 750, 'promo' => null, 'stock' => 10],

            // CHAMBRE
            ['nom' => 'Lit double moderne avec LED', 'cat' => 'Chambres à coucher', 'prix' => 1800, 'promo' => 1600, 'stock' => 5],
            ['nom' => 'Lit 160x200 bois massif', 'cat' => 'Chambres à coucher', 'prix' => 1500, 'promo' => null, 'stock' => 2],
            ['nom' => 'Table de chevet assortie', 'cat' => 'Chambres à coucher', 'prix' => 250, 'promo' => null, 'stock' => 14],
            ['nom' => 'Commode moderne 3 tiroirs', 'cat' => 'Commodes', 'prix' => 650, 'promo' => 590, 'stock' => 6],
            ['nom' => 'Dressing moderne sur mesure', 'cat' => 'Armoires', 'prix' => 3200, 'promo' => null, 'stock' => 0],

            // ARMOIRES
            ['nom' => 'Armoire 2 portes coulissantes', 'cat' => 'Armoires', 'prix' => 1400, 'promo' => null, 'stock' => 3],
            ['nom' => 'Armoire 3 portes avec miroir', 'cat' => 'Armoires', 'prix' => 1950, 'promo' => 1750, 'stock' => 2],
            ['nom' => 'Armoire moderne minimaliste', 'cat' => 'Armoires', 'prix' => 1600, 'promo' => null, 'stock' => 5],

            // MIROIRS
            ['nom' => 'Miroir mural rond', 'cat' => 'Miroirs', 'prix' => 180, 'promo' => null, 'stock' => 15],
            ['nom' => 'Miroir rectangulaire', 'cat' => 'Miroirs', 'prix' => 250, 'promo' => 199, 'stock' => 8],
            ['nom' => 'Miroir décoratif soleil', 'cat' => 'Miroirs', 'prix' => 150, 'promo' => null, 'stock' => 0],
            ['nom' => 'Miroir pleine longueur', 'cat' => 'Miroirs', 'prix' => 350, 'promo' => null, 'stock' => 4],

            // DÉCORATION
            ['nom' => 'Vase décoratif en céramique', 'cat' => 'Décoration', 'prix' => 85, 'promo' => null, 'stock' => 30],
            ['nom' => 'Horloge murale design', 'cat' => 'Décoration', 'prix' => 120, 'promo' => null, 'stock' => 12],
            ['nom' => 'Tableau décoratif abstrait', 'cat' => 'Décoration', 'prix' => 280, 'promo' => 220, 'stock' => 6],
            ['nom' => 'Objet décoratif en métal', 'cat' => 'Décoration', 'prix' => 95, 'promo' => null, 'stock' => 0],

            // BUREAUX
            ['nom' => 'Bureau moderne avec tiroirs', 'cat' => 'Bureaux', 'prix' => 650, 'promo' => null, 'stock' => 5],
            ['nom' => 'Bureau en bois de chêne', 'cat' => 'Bureaux', 'prix' => 950, 'promo' => 850, 'stock' => 2],
            ['nom' => 'Bureau compact pour ordinateur', 'cat' => 'Bureaux', 'prix' => 350, 'promo' => null, 'stock' => 10],

            // CONSOLES
            ['nom' => 'Console moderne en métal', 'cat' => 'Consoles', 'prix' => 450, 'promo' => null, 'stock' => 6],
            ['nom' => 'Console d\'entrée élégante', 'cat' => 'Consoles', 'prix' => 550, 'promo' => 450, 'stock' => 4],
            ['nom' => 'Console en bois vintage', 'cat' => 'Consoles', 'prix' => 600, 'promo' => null, 'stock' => 0],
        ];

        $refCounter = 1;

        foreach ($products as $p) {
            $categorie = Categorie::where('nom', $p['cat'])->first();

            if (!$categorie) {
                continue;
            }

            // Calcul du pourcentage de remise s'il y a un prix promo
            $remise = 0;
            if ($p['promo']) {
                $remise = round((($p['prix'] - $p['promo']) / $p['prix']) * 100, 2);
            }

            $reference = 'SEDI-' . strtoupper(substr(str_replace([' ', 'é', 'è', 'à'], '', $p['cat']), 0, 4)) . '-' . str_pad($refCounter, 3, '0', STR_PAD_LEFT);
            $refCounter++;

            Produit::firstOrCreate(
                ['reference' => $reference],
                [
                    'nom' => $p['nom'],
                    'categorie_id' => $categorie->id,
                    'description' => 'Superbe ' . strtolower($p['nom']) . ' de haute qualité, idéal pour aménager votre intérieur avec goût et modernité.',
                    'caracteristiques' => 'Matière premium. Design contemporain. Facile d\'entretien.',
                    'prix' => $p['prix'],
                    'remise' => $remise,
                    'prix_apres_remise' => $p['promo'] ?: $p['prix'],
                    'quantite_stock' => $p['stock'],
                    'actif' => true,
                    'nouveau' => (rand(1, 10) > 7) ? true : false,
                    'vedette' => (rand(1, 10) > 8) ? true : false,
                    'marque' => 'Zinet Eddar',
                ]
            );
        }
    }
}
