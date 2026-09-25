<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            // ── Profile fields ────────────────────────────────────────────
            'username'   => [
                'nullable',
                'string',
                'max:191',
                "unique:users,username,{$userId}",
            ],
            'first_name' => ['nullable', 'string', 'max:191'],
            'last_name'  => ['nullable', 'string', 'max:191'],

            // ── Password change fields (all three required together) ───────
            'current_password'              => [
                'nullable',
                'string',
                // If new_password is present, current_password becomes required
                'required_with:new_password',
            ],
            'new_password'                  => [
                'nullable',
                'string',
                'min:8',
                'confirmed',              // requires new_password_confirmation
                'required_with:current_password',
                'different:current_password', // new password must differ from old
            ],
            'new_password_confirmation'     => [
                'nullable',
                'string',
                'required_with:new_password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'                       => 'This username is already taken.',
            'current_password.required_with'        => 'The current password is required when changing your password.',
            'new_password.required_with'            => 'The new password is required when providing the current password.',
            'new_password.confirmed'                => 'The new password confirmation does not match.',
            'new_password.min'                      => 'The new password must be at least 8 characters.',
            'new_password.different'                => 'The new password must be different from the current password.',
            'new_password_confirmation.required_with' => 'Please confirm your new password.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
