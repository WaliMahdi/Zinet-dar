<?php

namespace App\Http\Requests\Catalog;

class UpdateCategorieRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $categorieId = $this->route('categorie') ? $this->route('categorie')->id : null;

        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'actif' => ['boolean'],
        ];
    }
}
