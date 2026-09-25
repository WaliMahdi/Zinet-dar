<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreCategorieRequest;
use App\Http\Requests\Catalog\UpdateCategorieRequest;
use App\Http\Resources\Catalog\CategorieResource;
use App\Http\Resources\Catalog\ProduitResource;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Categorie::withCount('produits')->orderBy('id')->get();
        return response()->json([
            'success' => true,
            'message' => 'Catégories récupérées avec succès.',
            'data' => CategorieResource::collection($categories)
        ]);
    }

    public function store(StoreCategorieRequest $request, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validated();
        
        $imageData = null;
        if (isset($data['image'])) {
            $imageData = $data['image'];
            unset($data['image']);
        }

        $categorie = Categorie::create($data);

        if ($request->hasFile('image')) {
            $publicId = "categorie_{$categorie->id}_main";
            $res = $cloudinary->uploadImage($request->file('image'), 'zinet-eddar/categories', $publicId);
            $categorie->update(['image' => $res['url']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Catégorie ajoutée avec succès.',
            'data' => new CategorieResource($categorie->fresh()->loadCount('produits'))
        ], 201);
    }

    public function show(Categorie $categorie): JsonResponse
    {
        $categorie->loadCount('produits');
        return response()->json([
            'success' => true,
            'message' => 'Catégorie récupérée avec succès.',
            'data' => new CategorieResource($categorie)
        ]);
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $publicId = "categorie_{$categorie->id}_main";
            $res = $cloudinary->uploadImage($request->file('image'), 'zinet-eddar/categories', $publicId);
            $data['image'] = $res['url'];
        } elseif ($request->boolean('remove_image')) {
            try {
                $cloudinary->deleteImage("zinet-eddar/categories/categorie_{$categorie->id}_main");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Erreur suppression image : " . $e->getMessage());
            }
            $data['image'] = null;
        }

        $categorie->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie modifiée avec succès.',
            'data' => new CategorieResource($categorie->fresh()->loadCount('produits'))
        ]);
    }

    public function destroy(Categorie $categorie, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        try {
            $cloudinary->deleteImage("zinet-eddar/categories/categorie_{$categorie->id}_main");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Ancienne catégorie non supprimée : " . $e->getMessage());
        }

        $categorie->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès.',
            'data' => null
        ]);
    }

    public function produits(Categorie $categorie): JsonResponse
    {
        $query = $categorie->produits()->with(['images']);

        $user = auth('sanctum')->user();
        $isRequestingAdmin = request()->boolean('for_admin');
        
        if (!$isRequestingAdmin || !$user || $user->email !== 'sedielectro@gmail.com') {
            $query->actifs();
        }

        $produits = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'message' => 'Produits récupérés avec succès.',
            'data' => ProduitResource::collection($produits)->response()->getData(true)
        ]);
    }
}
