<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PanierDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $produit = $this->produit;
        $prixUnitaire = $produit ? (float) ($produit->prix_apres_remise ?? $produit->prix) : 0;
        $montant = $prixUnitaire * $this->quantite;

        return [
            'id'            => $this->id,
            'produit_id'    => $this->produit_id,
            'nom'           => $produit ? $produit->nom : 'Produit inconnu',
            'image'         => $produit ? ($produit->image && str_starts_with($produit->image, 'http') ? $produit->image : ($produit->image ? url('storage/' . $produit->image) : null)) : null,
            'images'        => $produit && $produit->relationLoaded('images') ? \App\Http\Resources\Catalog\ImageProduitResource::collection($produit->images) : [],
            'prix_unitaire' => number_format($prixUnitaire, 3, '.', ''),
            'quantite'      => $this->quantite,
            'montant'       => number_format($montant, 3, '.', ''),
        ];
    }
}
