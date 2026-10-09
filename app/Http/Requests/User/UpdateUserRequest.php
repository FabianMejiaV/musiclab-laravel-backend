<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'unique:users,email'],
            'password' => [
                'sometimes',
                Password::min(8)
                    ->symbols()
                    ->mixedCase()
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede tener más de 100 caracteres',
            'email.email' => 'El correo electrónico debe ser una dirección de correo válida',
            'email.unique' => 'El correo electrónico ya está en uso',
            'password.mixed' => 'La contraseña debe contener al menos una letra mayúscula y una letra minúscula',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.symbols' => 'La contraseña debe tener al menos un simbolo'
        ];
    }
}
