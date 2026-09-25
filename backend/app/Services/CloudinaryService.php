<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    public function __construct()
    {
        \Cloudinary::config([
            'cloud_name' => config('services.cloudinary.cloud_name'),
            'api_key'    => config('services.cloudinary.api_key'),
            'api_secret' => config('services.cloudinary.api_secret'),
            'secure'     => true
        ]);
    }

    /**
     * Upload an image to Cloudinary.
     *
     * @param UploadedFile|string $file The file to upload (UploadedFile or local path)
     * @param string $folder The target folder in Cloudinary
     * @param string|null $publicId The specific public_id to use (optional)
     * @return array Contains 'url' and 'public_id'
     * @throws \Exception
     */
    public function uploadImage($file, string $folder, ?string $publicId = null): array
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $options = [
            'folder' => $folder,
            'resource_type' => 'image',
        ];

        if ($publicId) {
            $options['public_id'] = $publicId;
        }

        try {
            $response = \Cloudinary\Uploader::upload($path, $options);
            return [
                'url' => $response['secure_url'],
                'public_id' => $response['public_id']
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage(), [
                'folder' => $folder,
                'public_id' => $publicId
            ]);
            throw $e;
        }
    }

    /**
     * Delete an image from Cloudinary by its public_id.
     *
     * @param string $publicId
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        try {
            $response = \Cloudinary\Uploader::destroy($publicId);
            return isset($response['result']) && $response['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage(), [
                'public_id' => $publicId
            ]);
            throw new \Exception("Erreur lors de la suppression de l'image sur Cloudinary.");
        }
    }
}

