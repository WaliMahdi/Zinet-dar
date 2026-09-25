<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommandeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'produit_id' => ['required', 'exists:produits,id'],
            'nom_client' => ['required', 'string', 'max:255'],
            'telephone'  => ['required', 'string', 'max:30'],
            'adresse'    => ['required', 'string', 'max:1000'],
            'quantite'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'produit_id.required' => 'Le produit est requis.',
            'produit_id.exists'   => 'Le produit sélectionné est invalide.',
            'nom_client.required' => 'Le nom du client est requis.',
            'nom_client.max'      => 'Le nom ne doit pas dépasser 255 caractères.',
            'telephone.required'  => 'Le numéro de téléphone est requis.',
            'telephone.max'       => 'Le numéro de téléphone ne doit pas dépasser 30 caractères.',
            'adresse.required'    => 'L\'adresse est requise.',
            'adresse.max'         => 'L\'adresse ne doit pas dépasser 1000 caractères.',
            'quantite.required'   => 'La quantité est requise.',
            'quantite.integer'    => 'La quantité doit être un nombre entier.',
            'quantite.min'        => 'La quantité doit être au moins de 1.',
        ];
    }
}
