<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\ImageProduit;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $produits = Produit::all();

        foreach ($produits as $produit) {
            // Créer une image principale (placeholder via picsum)
            $imageUrl = 'https://picsum.photos/seed/' . $produit->id . '1/800/800';
            
            // Mettre à jour l'image principale du produit lui-même
            $produit->update([
                'image' => $imageUrl,
            ]);

            // Ajouter à la galerie : Image 1 (principale)
            ImageProduit::firstOrCreate(
                ['produit_id' => $produit->id, 'is_principale' => true],
                [
                    'url' => $imageUrl,
                    'public_id' => null,
                    'alt_text' => $produit->nom . ' - vue principale',
                ]
            );

            // Ajouter à la galerie : Image 2 (secondaire) - seulement pour un produit sur deux
            if ($produit->id % 2 == 0) {
                ImageProduit::firstOrCreate(
                    ['produit_id' => $produit->id, 'url' => 'https://picsum.photos/seed/' . $produit->id . '2/800/800'],
                    [
                        'is_principale' => false,
                        'public_id' => null,
                        'alt_text' => $produit->nom . ' - vue de côté',
                    ]
                );
            }
        }
    }
}
