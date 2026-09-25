<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageImage;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            [
                'section' => 'hero',
                'title' => 'Donnez du caractère',
                'description' => 'Des meubles élégants et des pièces soigneusement sélectionnées pour créer un espace qui vous ressemble.',
                'image_url' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&q=80',
            ],
            [
                'section' => 'about',
                'title' => 'Notre Histoire',
                'description' => 'Zinet Eddar, la référence du meuble en Tunisie depuis plus de 10 ans.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&q=80',
            ]
        ];

        foreach ($images as $img) {
            HomepageImage::firstOrCreate(
                ['section' => $img['section']],
                $img
            );
        }
    }
}
