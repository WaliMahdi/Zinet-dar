<?php

namespace Tests\Feature\Catalog;

use App\Models\ImageProduit;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageProduitTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Produit $produit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => 'sedielectro@gmail.com']);
        $this->produit = Produit::factory()->create();
    }

    public function test_can_upload_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('produit.jpg');

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/produits/{$this->produit->id}/images", [
            'image' => $file,
            'principale' => true,
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('images_produits', [
            'produit_id' => $this->produit->id,
            'principale' => 1,
        ]);
        
        $imagePath = ImageProduit::first()->chemin;
        Storage::disk('public')->assertExists($imagePath);
    }

    public function test_setting_primary_image_removes_old_primary(): void
    {
        Storage::fake('public');

        $image1 = ImageProduit::create([
            'produit_id' => $this->produit->id,
            'chemin' => 'test1.jpg',
            'principale' => true,
        ]);

        $image2 = ImageProduit::create([
            'produit_id' => $this->produit->id,
            'chemin' => 'test2.jpg',
            'principale' => false,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->patchJson("/api/produits/{$this->produit->id}/images/{$image2->id}/principale");

        $response->assertStatus(200);

        $this->assertFalse((bool)$image1->fresh()->principale);
        $this->assertTrue((bool)$image2->fresh()->principale);
    }
}
