<?php

namespace Database\Factories;

use App\Models\Commande;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommandeFactory extends Factory
{
    protected $model = Commande::class;

    public function definition(): array
    {
        $prixUnitaire = $this->faker->randomFloat(3, 10, 2000);
        $quantite     = $this->faker->numberBetween(1, 5);

        return [
            'user_id'       => User::factory(),
            'produit_id'    => Produit::factory(),
            'nom_client'    => $this->faker->name(),
            'telephone'     => $this->faker->numerify('########'),
            'adresse'       => $this->faker->address(),
            'quantite'      => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'montant_total' => $prixUnitaire * $quantite,
            'mode_paiement' => 'espece',
            'statut'        => 'en_attente',
            'note_admin'    => null,
            'date_traitement' => null,
        ];
    }
}
