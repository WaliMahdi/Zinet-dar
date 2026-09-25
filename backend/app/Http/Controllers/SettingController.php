<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\CloudinaryService;

class SettingController extends Controller
{
    public function logo()
    {
        $setting = Setting::where('key', 'logo')->first();
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    public function updateLogo(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:2048'
        ]);

        $setting = Setting::firstOrNew(['key' => 'logo']);

        if ($setting->public_id) {
            try {
                $cloudinary->deleteImage($setting->public_id);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Ancien logo non supprimé : " . $e->getMessage());
            }
        }

        $res = $cloudinary->uploadImage($request->file('logo'), 'zinet-eddar/logo');
        
        $setting->value = $res['url'];
        $setting->public_id = $res['public_id'];
        $setting->save();

        return response()->json(['success' => true, 'data' => $setting]);
    }
}
