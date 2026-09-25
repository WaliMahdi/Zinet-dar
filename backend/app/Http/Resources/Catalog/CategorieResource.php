<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategorieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->image && str_starts_with($this->image, 'http') ? $this->image : ($this->image ? url('storage/' . $this->image) : null),
            'actif' => $this->actif,
            'ordre' => $this->ordre,
            'produits_count' => $this->produits_count ?? 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
