<?php

namespace Tests\Feature\Catalog;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduitTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Categorie $categorie;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => 'sedielectro@gmail.com']);
        $this->categorie = Categorie::factory()->create();
    }

    public function test_can_create_produit_and_calculate_discount(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/produits', [
            'categorie_id' => $this->categorie->id,
            'nom' => 'Laptop Gamer',
            'prix' => 1000,
            'remise' => 20,
            'quantite_stock' => 10,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.prix', '1000.000')
                 ->assertJsonPath('data.prix_apres_remise', '800.000');
    }

    public function test_can_search_produit(): void
    {
        Produit::factory()->create(['nom' => 'Laptop Gamer', 'marque' => 'ASUS']);
        Produit::factory()->create(['nom' => 'Souris', 'marque' => 'Logitech']);

        $response = $this->getJson('/api/produits?q=ASUS');
        
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data.data');
        
        $this->assertEquals('Laptop Gamer', $response->json('data.data.0.nom'));
    }

    public function test_can_filter_by_price(): void
    {
        Produit::factory()->create(['prix_apres_remise' => 50]);
        Produit::factory()->create(['prix_apres_remise' => 150]);
        Produit::factory()->create(['prix_apres_remise' => 300]);

        $response = $this->getJson('/api/produits?prix_min=100&prix_max=200');
        
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data.data');
                 
        $this->assertEquals('150.000', $response->json('data.data.0.prix_apres_remise'));
    }

    public function test_can_update_stock(): void
    {
        $produit = Produit::factory()->create(['quantite_stock' => 5]);

        $response = $this->actingAs($this->user, 'sanctum')->patchJson("/api/produits/{$produit->id}/stock", [
            'quantite_stock' => 15,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.quantite_stock', 15);
                 
        $this->assertDatabaseHas('produits', ['id' => $produit->id, 'quantite_stock' => 15]);
    }

    public function test_cannot_apply_invalid_percentage_discount(): void
    {
        $produit = Produit::factory()->create(['prix' => 100]);

        $response = $this->actingAs($this->user, 'sanctum')->patchJson("/api/produits/{$produit->id}/remise", [
            'remise' => 150, // Invalid!
        ]);

        $response->assertStatus(422);
    }
}
