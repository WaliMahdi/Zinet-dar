<?php

namespace App\Http\Requests\Panier;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_client'  => ['required', 'string', 'max:255'],
            'telephone'   => ['required', 'string', 'max:30'],
            'adresse'     => ['required', 'string', 'max:1000'],
            'note_client' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_client.required' => 'Le nom du client est requis.',
            'nom_client.max'      => 'Le nom ne doit pas dépasser 255 caractères.',
            'telephone.required'  => 'Le numéro de téléphone est requis.',
            'telephone.max'       => 'Le numéro de téléphone ne doit pas dépasser 30 caractères.',
            'adresse.required'    => 'L\'adresse est requise.',
            'adresse.max'         => 'L\'adresse ne doit pas dépasser 1000 caractères.',
            'note_client.max'     => 'La note ne doit pas dépasser 5000 caractères.',
        ];
    }
}
