<?php

namespace App\Http\Requests\Catalog;

class StoreProduitRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'categorie_id'      => ['required', 'exists:categories,id'],
            'nom'             => ['required', 'string', 'max:255'],
            'marque'          => ['nullable', 'string', 'max:255'],
            'reference'       => ['nullable', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'caracteristiques' => ['nullable', 'string'],
            'prix'            => ['required', 'numeric', 'min:0'],
            'remise'          => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quantite_stock'  => ['required', 'integer', 'min:0'],
            'garantie'        => ['nullable', 'string', 'max:255'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images'          => ['nullable', 'array'],
            'images.*'        => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'actif'           => ['boolean'],
            'vedette'         => ['boolean'],
            'nouveau'         => ['boolean'],
        ];
    }
}
