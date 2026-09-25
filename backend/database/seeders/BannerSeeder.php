<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banniere;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Nouvelle Collection 2026',
                'description' => 'Découvrez notre toute nouvelle sélection de meubles pour réinventer votre intérieur.',
                'image_url' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80',
                'button_text' => 'Découvrir',
                'button_link' => '/boutique?nouveau=1',
                'is_active' => true,
            ],
            [
                'title' => 'Promotion Spéciale Salons',
                'description' => 'Jusqu\'à -30% sur une sélection de canapés et tables basses.',
                'image_url' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&q=80',
                'button_text' => 'Voir les offres',
                'button_link' => '/boutique?promo=1',
                'is_active' => false, // Seulement une active par défaut
            ]
        ];

        foreach ($banners as $b) {
            Banniere::firstOrCreate(
                ['title' => $b['title']],
                $b
            );
        }
    }
}
