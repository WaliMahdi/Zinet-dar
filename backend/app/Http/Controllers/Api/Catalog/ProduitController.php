<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProduitRequest;
use App\Http\Requests\Catalog\UpdateProduitRequest;
use App\Http\Resources\Catalog\ProduitResource;
use App\Models\Produit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    private function calculerPrixApresRemise(float $prix, ?float $remise = null): float
    {
        $remise = $remise ?? 0.0;
        $remise = min(max($remise, 0.0), 100.0);
        return max(0.0, $prix - ($prix * ($remise / 100.0)));
    }

    public function index(Request $request): JsonResponse
    {
        $query = Produit::query()->with(['images', 'categorie']);

        // Filtrer les produits inactifs pour le public
        $user = auth('sanctum')->user();
        $isRequestingAdmin = $request->boolean('for_admin');
        
        if (!$isRequestingAdmin || !$user || $user->email !== 'sedielectro@gmail.com') {
            $query->actifs();
        }

        // Recherche & Filtres
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $searchTerm = '%' . $request->q . '%';
                $q->where('nom', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('caracteristiques', 'like', $searchTerm)
                  ->orWhere('reference', 'like', $searchTerm)
                  ->orWhere('marque', 'like', $searchTerm)
                  ->orWhereHas('categorie', function ($catQuery) use ($searchTerm) {
                      $catQuery->where('nom', 'like', $searchTerm);
                  });
            });
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('prix_min')) {
            $query->where('prix_apres_remise', '>=', $request->prix_min);
        }

        if ($request->filled('prix_max')) {
            $query->where('prix_apres_remise', '<=', $request->prix_max);
        }

        if ($request->boolean('vedette')) {
            $query->where('vedette', true);
        }

        if ($request->boolean('nouveau')) {
            $query->where('nouveau', true);
        }

        if ($request->boolean('promo')) {
            $query->where('remise', '>', 0);
        }

        // Tri
        $sort  = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $allowedSorts = ['prix', 'prix_apres_remise', 'nom', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order === 'asc' ? 'asc' : 'desc');
        }

        $produits = $query->paginate((int) $request->get('per_page', 12));

        return response()->json([
            'success'    => true,
            'message'    => 'Produits récupérés avec succès.',
            'data'       => ProduitResource::collection($produits),
            'pagination' => [
                'current_page' => $produits->currentPage(),
                'last_page'    => $produits->lastPage(),
                'per_page'     => $produits->perPage(),
                'total'        => $produits->total(),
                'from'         => $produits->firstItem(),
                'to'           => $produits->lastItem(),
            ],
        ]);
    }

    public function store(StoreProduitRequest $request, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validated();

        $prix   = (float) $data['prix'];
        $remise = isset($data['remise']) ? (float) $data['remise'] : null;

        $data['remise']            = $remise ?? 0.0;
        $data['prix_apres_remise'] = $this->calculerPrixApresRemise($prix, $remise);

        // Retirer l'image des data pour le create
        $imageData = null;
        if (isset($data['image'])) {
            unset($data['image']);
        }

        $produit = Produit::create($data);
        
        // Uploader l'image avec un ID unique lié au produit
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $publicId = "produit_{$produit->id}_main";
            $res = $cloudinary->uploadImage($request->file('image'), 'zinet-eddar/products', $publicId);
            $produit->update(['image' => $res['url']]);
        }

        // Ajouter l'upload des images supplémentaires de la galerie
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $uniqueId = uniqid();
                    $galleryPublicId = "produit_{$produit->id}_gallery_{$uniqueId}";
                    $res = $cloudinary->uploadImage($file, "zinet-eddar/products", $galleryPublicId);
                    $produit->images()->create([
                        'url' => $res['url'],
                        'public_id' => $res['public_id'],
                        'is_principale' => false,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté avec succès.',
            'data'    => new ProduitResource($produit->fresh()),
        ], 201);
    }

    public function show(Produit $produit): JsonResponse
    {
        $user = auth('sanctum')->user();
        $isRequestingAdmin = request()->boolean('for_admin');
        
        if (!$produit->actif && (!$isRequestingAdmin || !$user || $user->email !== 'sedielectro@gmail.com')) {
            abort(404, 'Produit non disponible.');
        }

        $produit->load(['images', 'categorie']);

        return response()->json([
            'success' => true,
            'message' => 'Produit récupéré avec succès.',
            'data'    => new ProduitResource($produit),
        ]);
    }

    public function update(UpdateProduitRequest $request, Produit $produit, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validated();

        $prix   = isset($data['prix']) ? (float) $data['prix'] : (float) $produit->prix;
        $remise = array_key_exists('remise', $data)
            ? (isset($data['remise']) ? (float) $data['remise'] : null)
            : (float) $produit->remise;

        $data['prix_apres_remise'] = $this->calculerPrixApresRemise($prix, $remise);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Définir un public_id fixe basé sur l'ID du produit pour écraser l'ancienne image sur Cloudinary
            $publicId = "produit_{$produit->id}_main";
            $res = $cloudinary->uploadImage($request->file('image'), "zinet-eddar/products", $publicId);
            $data['image'] = $res['url'];
        }

        $produit->update($data);

        // Ajouter l'upload des nouvelles images supplémentaires de la galerie
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $uniqueId = uniqid();
                    $galleryPublicId = "produit_{$produit->id}_gallery_{$uniqueId}";
                    $res = $cloudinary->uploadImage($file, "zinet-eddar/products", $galleryPublicId);
                    $produit->images()->create([
                        'url' => $res['url'],
                        'public_id' => $res['public_id'],
                        'is_principale' => false,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit modifié avec succès.',
            'data'    => new ProduitResource($produit->fresh(['images', 'categorie'])),
        ]);
    }

    public function destroy(Produit $produit, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        try {
            $cloudinary->deleteImage("zinet-eddar/products/produit_{$produit->id}_main");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Ancienne image non supprimée : " . $e->getMessage());
        }

        $produit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé avec succès.',
            'data'    => null,
        ]);
    }

    public function stock(Request $request, Produit $produit): JsonResponse
    {
        $data = $request->validate([
            'quantite_stock' => ['required', 'integer', 'min:0'],
        ]);

        $produit->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Stock modifié avec succès.',
            'data'    => new ProduitResource($produit),
        ]);
    }

    public function remise(Request $request, Produit $produit): JsonResponse
    {
        $data = $request->validate([
            'remise' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['prix_apres_remise'] = $this->calculerPrixApresRemise(
            (float) $produit->prix,
            (float) $data['remise']
        );

        $produit->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Remise appliquée avec succès.',
            'data'    => new ProduitResource($produit),
        ]);
    }

    public function statut(Request $request, Produit $produit): JsonResponse
    {
        $data = $request->validate([
            'actif'   => ['boolean'],
            'vedette' => ['boolean'],
            'nouveau' => ['boolean'],
        ]);

        $produit->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Statut du produit modifié avec succès.',
            'data'    => new ProduitResource($produit),
        ]);
    }
}
