<?php

namespace App\Http\Requests\Catalog;

class UpdateImageProduitRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'texte_alternatif' => ['nullable', 'string', 'max:255'],
            'principale' => ['boolean'],
            'ordre' => ['integer', 'min:0'],
        ];
    }
}
