<?php

namespace App\Http\Requests\Catalog;

class StoreImageProduitRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'texte_alternatif' => ['nullable', 'string', 'max:255'],
            'principale' => ['boolean'],
            'ordre' => ['integer', 'min:0'],
        ];
    }
}
