<?php

namespace Tests\Feature\Catalog;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorieTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => 'sedielectro@gmail.com']);
    }

    public function test_can_list_categories(): void
    {
        Categorie::factory()->count(3)->create();
        
        $response = $this->getJson('/api/categories');
        
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_categorie(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/categories', [
            'nom' => 'Test Categorie',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nom', 'Test Categorie');
                 
        $this->assertDatabaseHas('categories', ['nom' => 'Test Categorie']);
    }

    public function test_cannot_create_categorie_unauthenticated(): void
    {
        $response = $this->postJson('/api/categories', [
            'nom' => 'Test Categorie',
        ]);

        $response->assertStatus(401);
    }
}
