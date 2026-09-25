<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'categorie_id'     => $this->categorie_id,
            'nom'              => $this->nom,
            'marque'           => $this->marque,
            'reference'        => $this->reference,
            'description'      => $this->description,
            'caracteristiques' => $this->caracteristiques,
            'prix'             => $this->prix !== null ? number_format((float) $this->prix, 3, '.', '') : null,
            'remise'           => $this->remise !== null ? number_format((float) $this->remise, 2, '.', '') : null,
            'prix_apres_remise' => $this->prix_apres_remise !== null ? number_format((float) $this->prix_apres_remise, 3, '.', '') : null,
            'quantite_stock'   => $this->quantite_stock,
            'garantie'         => $this->garantie,
            'image'            => $this->image && str_starts_with($this->image, 'http') ? $this->image : ($this->image ? url('storage/' . $this->image) : null),
            'actif'            => $this->actif,
            'vedette'          => $this->vedette,
            'nouveau'          => $this->nouveau,

            // Relations
            'categorie'     => new CategorieResource($this->whenLoaded('categorie')),
            'images'        => ImageProduitResource::collection($this->whenLoaded('images')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
