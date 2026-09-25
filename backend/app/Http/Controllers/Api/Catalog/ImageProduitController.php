<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreImageProduitRequest;
use App\Http\Requests\Catalog\UpdateImageProduitRequest;
use App\Http\Resources\Catalog\ImageProduitResource;
use App\Models\ImageProduit;
use App\Models\Produit;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageProduitController extends Controller
{
    public function store(StoreImageProduitRequest $request, Produit $produit, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validated();
        
        $uniqueId = uniqid();
        $galleryPublicId = "produit_{$produit->id}_gallery_{$uniqueId}";
        $res = $cloudinary->uploadImage($request->file('image'), "zinet-eddar/products", $galleryPublicId);
        $data['url'] = $res['url'];
        $data['public_id'] = $res['public_id'];
        
        if ($data['principale'] ?? false) {
            $produit->images()->update(['is_principale' => false]);
            $data['is_principale'] = true;
        }

        $image = $produit->images()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Image ajoutée avec succès.',
            'data' => new ImageProduitResource($image)
        ], 201);
    }

    public function destroy(Produit $produit, ImageProduit $image, \App\Services\CloudinaryService $cloudinary): JsonResponse
    {
        if ($image->produit_id !== $produit->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette image n\'appartient pas à ce produit.'
            ], 403);
        }

        if ($image->public_id) {
            try {
                $cloudinary->deleteImage($image->public_id);
            } catch (\Exception $e) {
                // Return clear error if delete fails as requested by user
                return response()->json([
                    'success' => false,
                    'message' => "Erreur Cloudinary : impossible de supprimer l'image."
                ], 500);
            }
        }
        
        $image->delete();

        // S'il n'y a plus d'image principale mais qu'il reste d'autres images, en définir une nouvelle
        if ($image->is_principale && $produit->images()->count() > 0) {
            $produit->images()->first()->update(['is_principale' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Image supprimée avec succès.',
            'data' => null
        ]);
    }

    public function principale(Produit $produit, ImageProduit $image): JsonResponse
    {
        if ($image->produit_id !== $produit->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cette image n\'appartient pas à ce produit.'
            ], 403);
        }

        $produit->images()->update(['is_principale' => false]);
        $image->update(['is_principale' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Image principale définie avec succès.',
            'data' => new ImageProduitResource($image)
        ]);
    }
}
