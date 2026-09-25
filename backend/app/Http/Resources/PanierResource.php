<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PanierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $details = $this->whenLoaded('details');
        
        $sousTotal = 0;
        $nombreArticles = 0;

        if ($details && !is_string($details)) {
            foreach ($details as $detail) {
                $produit = $detail->produit;
                $prixUnitaire = $produit ? (float) ($produit->prix_apres_remise ?? $produit->prix) : 0;
                $sousTotal += $prixUnitaire * $detail->quantite;
                $nombreArticles += $detail->quantite;
            }
        }

        return [
            'id'              => $this->id,
            'items'           => PanierDetailResource::collection($this->whenLoaded('details')),
            'sous_total'      => number_format($sousTotal, 3, '.', ''),
            'montant_total'   => number_format($sousTotal, 3, '.', ''),
            'nombre_articles' => $nombreArticles,
        ];
    }
}
