<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $normalUser;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->normalUser = User::factory()->create([
            'email' => 'normal@example.com',
            'password' => Hash::make('password123')
        ]);
        
        $this->adminUser = User::factory()->create([
            'email' => 'sedielectro@gmail.com',
            'password' => Hash::make('SediElectro123@')
        ]);
    }

    public function test_1_unauthenticated_user_cannot_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'nom' => 'Test',
        ]);

        $response->assertStatus(401);
    }

    public function test_2_normal_authenticated_user_cannot_create_category(): void
    {
        $response = $this->actingAs($this->normalUser, 'sanctum')->postJson('/api/categories', [
            'nom' => 'Test',
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Access denied. Admin privileges required.'
                 ]);
    }

    public function test_3_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/categories', [
            'nom' => 'Test Category',
        ]);

        $response->assertStatus(201);
    }

    public function test_4_normal_user_cannot_update_product(): void
    {
        $produit = Produit::factory()->create();

        $response = $this->actingAs($this->normalUser, 'sanctum')->putJson("/api/produits/{$produit->id}", [
            'nom' => 'Updated Product',
        ]);

        $response->assertStatus(403);
    }

    public function test_5_admin_can_update_product(): void
    {
        $produit = Produit::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')->patchJson("/api/produits/{$produit->id}/stock", [
            'quantite_stock' => 50,
        ]);

        $response->assertStatus(200);
    }

    public function test_6_normal_user_cannot_delete_category(): void
    {
        $categorie = Categorie::factory()->create();

        $response = $this->actingAs($this->normalUser, 'sanctum')->deleteJson("/api/categories/{$categorie->id}");

        $response->assertStatus(403);
    }

    public function test_7_admin_can_delete_category(): void
    {
        $categorie = Categorie::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')->deleteJson("/api/categories/{$categorie->id}");

        $response->assertStatus(200);
    }

    public function test_8_unauthenticated_user_can_read_products(): void
    {
        Produit::factory()->count(2)->create();

        $response = $this->getJson('/api/produits');

        $response->assertStatus(200);
    }

    public function test_9_admin_logs_in_successfully(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'sedielectro@gmail.com',
            'password' => 'SediElectro123@',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message', 'token', 'user' => ['is_admin']])
                 ->assertJsonPath('user.is_admin', true);
    }

    public function test_10_normal_user_is_not_admin_and_cannot_access_crud(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'normal@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('user.is_admin', false);
                 
        $token = $response->json('token');
        
        $responseCrud = $this->withHeader('Authorization', "Bearer $token")
                             ->postJson('/api/categories', ['nom' => 'Test']);
                             
        $responseCrud->assertStatus(403);
    }
}
