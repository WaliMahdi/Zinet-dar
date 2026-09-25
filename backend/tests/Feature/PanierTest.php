<?php

namespace Tests\Feature;

use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanierTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Produit $produit1;
    private Produit $produit2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();

        $this->produit1 = Produit::factory()->create([
            'prix' => 100,
            'prix_apres_remise' => 100,
            'quantite_stock' => 10,
            'actif' => true,
        ]);

        $this->produit2 = Produit::factory()->create([
            'prix' => 200,
            'prix_apres_remise' => 200,
            'quantite_stock' => 5,
            'actif' => true,
        ]);
    }

    public function test_authenticated_user_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('panier.nombre_articles', 2)
                 ->assertJsonPath('panier.items.0.produit_id', $this->produit1->id)
                 ->assertJsonPath('panier.sous_total', "200.000");
    }

    public function test_adding_same_product_twice_increases_quantity()
    {
        $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 2,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 3,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('panier.nombre_articles', 5)
                 ->assertJsonPath('panier.items.0.quantite', 5);
    }

    public function test_user_can_view_his_cart()
    {
        $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 1,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/panier');

        $response->assertStatus(200)
                 ->assertJsonPath('panier.nombre_articles', 1);
    }

    public function test_user_can_update_cart_quantity()
    {
        $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 1,
        ]);

        $response = $this->actingAs($this->user)->patchJson('/api/panier/' . $this->produit1->id . '/quantite', [
            'quantite' => 4,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('panier.nombre_articles', 4);
    }

    public function test_user_can_remove_a_product()
    {
        $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 1,
        ]);

        $response = $this->actingAs($this->user)->deleteJson('/api/panier/' . $this->produit1->id);

        $response->assertStatus(200)
                 ->assertJsonPath('panier.nombre_articles', 0);
    }

    public function test_user_can_empty_the_cart()
    {
        $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 1,
        ]);
        
        $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit2->id,
            'quantite'   => 1,
        ]);

        $response = $this->actingAs($this->user)->deleteJson('/api/panier/vider');

        $response->assertStatus(200)
                 ->assertJsonPath('panier.nombre_articles', 0);
    }

    public function test_insufficient_stock_is_rejected_on_add()
    {
        $response = $this->actingAs($this->user)->postJson('/api/panier/ajouter', [
            'produit_id' => $this->produit1->id,
            'quantite'   => 100, // Stock is 10
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    public function test_unauthenticated_user_receives_401()
    {
        $response = $this->getJson('/api/panier');
        $response->assertStatus(401);
    }
}
