<?php

namespace App\Http\Resources;

use App\Http\Resources\Catalog\ProduitResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommandeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'nom_client'      => $this->nom_client,
            'telephone'       => $this->telephone,
            'adresse'         => $this->adresse,
            'sous_total'      => number_format((float) $this->sous_total, 3, '.', ''),
            'montant_total'   => number_format((float) $this->montant_total, 3, '.', ''),
            'mode_paiement'   => $this->mode_paiement,
            'statut'          => $this->statut,
            'note_client'     => $this->note_client,
            'note_admin'      => $this->note_admin,
            'date_traitement' => $this->date_traitement?->toISOString(),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),

            // Relations
            'details' => CommandeDetailResource::collection($this->whenLoaded('details')),
            'user'    => new UserResource($this->whenLoaded('user')),
        ];
    }
}
