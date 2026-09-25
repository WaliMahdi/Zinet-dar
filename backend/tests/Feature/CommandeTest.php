<?php

namespace Tests\Feature;

use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Panier;
use App\Models\PanierDetail;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeTest extends TestCase
{
    use RefreshDatabase;

    private User $normalUser;
    private User $adminUser;
    private User $otherUser;
    private Produit $produit1;
    private Produit $produit2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->normalUser = User::factory()->create(['email' => 'client@example.com']);
        $this->otherUser  = User::factory()->create(['email' => 'other@example.com']);
        $this->adminUser  = User::factory()->create(['email' => 'sedielectro@gmail.com']);

        $this->produit1 = Produit::factory()->create([
            'prix'             => 100.000,
            'prix_apres_remise' => 100.000,
            'quantite_stock'   => 10,
            'actif'            => true,
        ]);

        $this->produit2 = Produit::factory()->create([
            'prix'             => 200.000,
            'prix_apres_remise' => 200.000,
            'quantite_stock'   => 5,
            'actif'            => true,
        ]);
    }

    private function addItemsToCart(User $user)
    {
        $panier = Panier::create(['user_id' => $user->id]);
        
        PanierDetail::create([
            'panier_id' => $panier->id,
            'produit_id' => $this->produit1->id,
            'quantite' => 2,
        ]);

        PanierDetail::create([
            'panier_id' => $panier->id,
            'produit_id' => $this->produit2->id,
            'quantite' => 1,
        ]);
    }

    public function test_cannot_checkout_empty_cart(): void
    {
        $response = $this->actingAs($this->normalUser)->postJson('/api/panier/commander', [
            'nom_client' => 'Mahdi',
            'telephone'  => '20123456',
            'adresse'    => 'Sfax',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    public function test_user_can_create_order_from_cart(): void
    {
        $this->addItemsToCart($this->normalUser);

        $response = $this->actingAs($this->normalUser)->postJson('/api/panier/commander', [
            'nom_client' => 'Mahdi',
            'telephone'  => '20123456',
            'adresse'    => 'Sfax',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.statut', 'en_attente')
                 ->assertJsonPath('data.sous_total', '400.000'); // 2*100 + 1*200

        // Assert cart is emptied
        $this->assertDatabaseCount('panier_details', 0);

        // Assert stock is reduced
        $this->assertDatabaseHas('produits', [
            'id' => $this->produit1->id,
            'quantite_stock' => 8, // 10 - 2
        ]);
    }

    public function test_client_can_view_own_orders(): void
    {
        $this->addItemsToCart($this->normalUser);
        $this->actingAs($this->normalUser)->postJson('/api/panier/commander', [
            'nom_client' => 'Mahdi', 'telephone' => '20123456', 'adresse' => 'Sfax',
        ]);

        $response = $this->actingAs($this->normalUser)->getJson('/api/mes-commandes');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    public function test_admin_can_update_statut_and_stock_is_managed(): void
    {
        $this->addItemsToCart($this->normalUser);
        $response = $this->actingAs($this->normalUser)->postJson('/api/panier/commander', [
            'nom_client' => 'Mahdi', 'telephone' => '20123456', 'adresse' => 'Sfax',
        ]);
        
        $commandeId = $response->json('data.id');

        // Transition to annulee should restore stock
        $this->actingAs($this->adminUser)->patchJson("/api/admin/commandes/{$commandeId}/statut", [
            'statut' => 'annulee',
        ]);

        $this->assertDatabaseHas('produits', [
            'id' => $this->produit1->id,
            'quantite_stock' => 10, // Restored
        ]);

        // Transition from annulee to confirmee should decrement stock again
        $this->actingAs($this->adminUser)->patchJson("/api/admin/commandes/{$commandeId}/statut", [
            'statut' => 'confirmee',
        ]);

        $this->assertDatabaseHas('produits', [
            'id' => $this->produit1->id,
            'quantite_stock' => 8, // Decremented again
        ]);
    }

    public function test_no_installment_fields_in_order_response(): void
    {
        $this->addItemsToCart($this->normalUser);
        $response = $this->actingAs($this->normalUser)->postJson('/api/panier/commander', [
            'nom_client' => 'Mahdi', 'telephone' => '20123456', 'adresse' => 'Sfax',
        ]);

        $responseData = $response->json('data');

        $this->assertArrayNotHasKey('taux_facilite', $responseData);
        $this->assertArrayNotHasKey('total_facilite', $responseData);
    }
}
