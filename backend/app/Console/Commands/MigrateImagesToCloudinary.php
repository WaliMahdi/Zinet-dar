<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produit;
use App\Models\ImageProduit;
use App\Models\Categorie;
use App\Models\Setting;
use App\Models\HomepageImage;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToCloudinary extends Command
{
    protected $signature = 'images:migrate-to-cloudinary';
    protected $description = 'Migrate all local images to Cloudinary without deleting local files.';

    protected $stats = [
        'detected' => 0,
        'copied' => 0,
        'already_migrated' => 0,
        'ignored' => 0,
        'failed' => 0,
        'errors' => []
    ];

    public function handle(CloudinaryService $cloudinary)
    {
        $this->info("Démarrage de la migration vers Cloudinary...");

        // 1. Catégories
        $this->info("--- Catégories ---");
        $categories = Categorie::whereNotNull('image')->get();
        foreach ($categories as $cat) {
            $this->stats['detected']++;
            if ($cat->image_public_id) {
                $this->stats['already_migrated']++;
                continue;
            }
            if (Storage::disk('public')->exists($cat->image)) {
                try {
                    $res = $cloudinary->uploadImage(Storage::disk('public')->path($cat->image), 'zinet-eddar/categories');
                    $cat->update([
                        'image' => $res['url'],
                        'image_public_id' => $res['public_id']
                    ]);
                    $this->stats['copied']++;
                    $this->line(" Catégorie #{$cat->id} migrée.");
                } catch (\Exception $e) {
                    $this->stats['failed']++;
                    $this->stats['errors'][] = "Catégorie #{$cat->id} - " . $e->getMessage();
                }
            } else {
                $this->stats['ignored']++;
            }
        }

        // 2. Produits (Image principale)
        $this->info("--- Produits (Principale) ---");
        $produits = Produit::whereNotNull('image')->get();
        foreach ($produits as $prod) {
            $this->stats['detected']++;
            if ($prod->public_id) {
                $this->stats['already_migrated']++;
                continue;
            }
            if (Storage::disk('public')->exists($prod->image)) {
                try {
                    $res = $cloudinary->uploadImage(Storage::disk('public')->path($prod->image), "zinet-eddar/products/{$prod->id}");
                    $prod->update([
                        'image' => $res['url'],
                        'public_id' => $res['public_id']
                    ]);
                    $this->stats['copied']++;
                    $this->line(" Produit #{$prod->id} migré.");
                } catch (\Exception $e) {
                    $this->stats['failed']++;
                    $this->stats['errors'][] = "Produit #{$prod->id} - " . $e->getMessage();
                }
            } else {
                $this->stats['ignored']++;
            }
        }

        // 3. Images Produits (Galerie)
        $this->info("--- Images Produits (Galerie) ---");
        $images = ImageProduit::all();
        foreach ($images as $img) {
            $this->stats['detected']++;
            if ($img->public_id) {
                $this->stats['already_migrated']++;
                continue;
            }
            // "url" a remplacé "chemin" dans la base
            if (Storage::disk('public')->exists($img->url)) {
                try {
                    $res = $cloudinary->uploadImage(Storage::disk('public')->path($img->url), "zinet-eddar/products/{$img->produit_id}");
                    $img->update([
                        'url' => $res['url'],
                        'public_id' => $res['public_id']
                    ]);
                    $this->stats['copied']++;
                    $this->line(" ImageProduit #{$img->id} migrée.");
                } catch (\Exception $e) {
                    $this->stats['failed']++;
                    $this->stats['errors'][] = "ImageProduit #{$img->id} - " . $e->getMessage();
                }
            } else {
                $this->stats['ignored']++;
            }
        }

        // 4. Statiques (Homepage & Bannières factices & Logo)
        $this->info("--- Images Statiques ---");
        $staticImages = [
            [
                'type' => 'homepage',
                'path' => public_path('../../frontend/public/hero-living-room.jpg'),
                'folder' => 'zinet-eddar/homepage',
                'db_callback' => function($res) {
                    HomepageImage::firstOrCreate(
                        ['section' => 'hero'],
                        ['image_url' => $res['url'], 'image_public_id' => $res['public_id'], 'title' => 'Hero']
                    );
                }
            ],
            [
                'type' => 'banner',
                'path' => public_path('../../frontend/src/assets/images/product-placeholder.jpg'), // example
                'folder' => 'zinet-eddar/other',
                'db_callback' => null
            ]
        ];

        foreach ($staticImages as $static) {
            $this->stats['detected']++;
            if (file_exists($static['path'])) {
                try {
                    $res = $cloudinary->uploadImage($static['path'], $static['folder']);
                    if ($static['db_callback']) {
                        $static['db_callback']($res);
                    }
                    $this->stats['copied']++;
                    $this->line(" Image statique ({$static['type']}) migrée.");
                } catch (\Exception $e) {
                    $this->stats['failed']++;
                    $this->stats['errors'][] = "Statique ({$static['type']}) - " . $e->getMessage();
                }
            } else {
                $this->stats['ignored']++;
            }
        }

        // Résumé
        $this->info("\n=================================");
        $this->info("RÉSUMÉ DE LA MIGRATION CLOUDINARY");
        $this->info("=================================");
        $this->line("Images détectées : " . $this->stats['detected']);
        $this->line("Images copiées : " . $this->stats['copied']);
        $this->line("Images déjà migrées : " . $this->stats['already_migrated']);
        $this->line("Images ignorées : " . $this->stats['ignored']);
        $this->line("Images échouées : " . $this->stats['failed']);
        
        if (count($this->stats['errors']) > 0) {
            $this->error("\nListe des erreurs :");
            foreach ($this->stats['errors'] as $err) {
                $this->line("- $err");
            }
        }

        return Command::SUCCESS;
    }
}
