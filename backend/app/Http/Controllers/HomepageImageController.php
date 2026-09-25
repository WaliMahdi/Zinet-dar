<?php

namespace App\Http\Controllers;

use App\Models\HomepageImage;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;

class HomepageImageController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => HomepageImage::all()
        ]);
    }

    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'section' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $res = $cloudinary->uploadImage($request->file('image'), 'zinet-eddar/homepage');
        
        $homepageImage = HomepageImage::create(array_merge($request->except('image'), [
            'image_url' => $res['url'],
            'image_public_id' => $res['public_id']
        ]));

        return response()->json(['success' => true, 'data' => $homepageImage], 201);
    }

    public function destroy(HomepageImage $homepageImage, CloudinaryService $cloudinary)
    {
        if ($homepageImage->image_public_id) {
            try {
                $cloudinary->deleteImage($homepageImage->image_public_id);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => "Erreur Cloudinary"], 500);
            }
        }
        $homepageImage->delete();
        return response()->json(['success' => true]);
    }
}
