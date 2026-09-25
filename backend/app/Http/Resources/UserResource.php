<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a User model into a consistent, password-free JSON structure
 * used across all authentication endpoints.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdmin = $this->email === 'sedielectro@gmail.com';

        return [
            'id'                => $this->id,
            'email'             => $this->email,
            'username'          => $this->username,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
            'is_admin'          => $isAdmin,
            'role'              => $isAdmin ? 'admin' : 'client',
        ];
    }
}
