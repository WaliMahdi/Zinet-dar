<?php

namespace App\Http\Resources;

use App\Http\Resources\Catalog\ProduitResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'produit_id'        => $this->produit_id,
            'nom_produit'       => $this->nom_produit,
            'reference_produit' => $this->reference_produit,
            'prix_unitaire'     => number_format((float) $this->prix_unitaire, 3, '.', ''),
            'quantite'          => $this->quantite,
            'montant'           => number_format((float) $this->montant, 3, '.', ''),
            'produit'           => new ProduitResource($this->whenLoaded('produit')),
        ];
    }
}
