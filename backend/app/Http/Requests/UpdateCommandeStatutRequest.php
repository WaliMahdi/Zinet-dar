<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommandeStatutRequest extends FormRequest
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
            'statut'     => ['required', 'in:en_attente,confirmee,livree,annulee'],
            'note_admin' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut est requis.',
            'statut.in'       => 'Le statut doit être l\'une des valeurs suivantes : en_attente, confirmee, livree, annulee.',
            'note_admin.max'  => 'La note admin ne doit pas dépasser 5000 caractères.',
        ];
    }
}
