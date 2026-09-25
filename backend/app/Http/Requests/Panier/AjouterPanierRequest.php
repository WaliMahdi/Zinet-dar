<?php

namespace App\Http\Requests\Panier;

use Illuminate\Foundation\Http\FormRequest;

class AjouterPanierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produit_id' => ['required', 'exists:produits,id'],
            'quantite'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'produit_id.required' => 'Le produit est requis.',
            'produit_id.exists'   => 'Le produit sélectionné est invalide.',
            'quantite.required'   => 'La quantité est requise.',
            'quantite.integer'    => 'La quantité doit être un nombre entier.',
            'quantite.min'        => 'La quantité doit être d\'au moins 1.',
        ];
    }
}
