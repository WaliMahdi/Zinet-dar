<?php

namespace App\Http\Controllers;

use App\Models\Banniere;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;

class BanniereController extends Controller
{
    public function index(Request $request)
    {
        $query = Banniere::orderBy('ordre');
        
        if (!$request->has('all')) {
            $query->where('is_active', 1);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function getBySection($section)
    {
        $validSections = ['accueil', 'boutique', 'nouveautes', 'promotions', 'categories'];
        if (!in_array($section, $validSections)) {
            return response()->json(['success' => false, 'message' => 'Section invalide'], 400);
        }

        $banniere = Banniere::where('section', $section)
                            ->where('is_active', 1)
                            ->first();

        return response()->json([
            'success' => true,
            'data' => $banniere ? [
                'id' => $banniere->id,
                'image_url' => $banniere->image_url,
                'title' => $banniere->title,
                'section' => $banniere->section,
                'is_active' => $banniere->is_active
            ] : null
        ]);
    }

    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'title' => 'nullable|string|max:255',
            'section' => 'required|in:accueil,boutique,nouveautes,promotions,categories',
            'is_active' => 'boolean'
        ]);

        $res = $cloudinary->uploadImage($request->file('image'), 'zinet-eddar/banners');
        
        $banniere = Banniere::create(array_merge($request->except('image'), [
            'image_url' => $res['url'],
            'image_public_id' => $res['public_id']
        ]));

        return response()->json(['success' => true, 'data' => $banniere], 201);
    }

    public function update(Request $request, Banniere $banniere, CloudinaryService $cloudinary)
    {
        $request->validate([
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:5120',
            'title' => 'nullable|string|max:255',
            'section' => 'required|in:accueil,boutique,nouveautes,promotions,categories',
            'is_active' => 'boolean'
        ]);

        $data = $request->except('image', '_method');

        if ($request->hasFile('image')) {
            $res = $cloudinary->uploadImage($request->file('image'), 'zinet-eddar/banners');
            
            $oldPublicId = $banniere->image_public_id;
            
            $data['image_url'] = $res['url'];
            $data['image_public_id'] = $res['public_id'];
            
            $banniere->update($data);
            
            // Supprimer l'ancienne image SEULEMENT après le succès
            if ($oldPublicId) {
                try {
                    $cloudinary->deleteImage($oldPublicId);
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                }
            }
        } else {
            $banniere->update($data);
        }

        return response()->json(['success' => true, 'data' => $banniere]);
    }

    public function updateStatus(Request $request, Banniere $banniere)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $banniere->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true, 'data' => $banniere]);
    }

    public function destroy(Banniere $banniere, CloudinaryService $cloudinary)
    {
        if ($banniere->image_public_id) {
            try {
                $cloudinary->deleteImage($banniere->image_public_id);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => "Erreur Cloudinary"], 500);
            }
        }
        $banniere->delete();
        return response()->json(['success' => true]);
    }
}
