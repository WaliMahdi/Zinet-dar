<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProduitFactory extends Factory
{
    public function definition(): array
    {
        $prix = $this->faker->randomFloat(3, 10, 1000);
        return [
            'categorie_id' => Categorie::factory(),
            'nom' => $this->faker->words(3, true),
            'reference' => strtoupper($this->faker->lexify('???-???')),
            'marque' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'caracteristiques' => $this->faker->paragraph(),
            'prix' => $prix,
            'remise' => 0,
            'prix_apres_remise' => $prix,
            'quantite_stock' => $this->faker->numberBetween(10, 100),
            'garantie' => '1 an',
            'image' => null,
            'actif' => true,
        ];
    }
}
