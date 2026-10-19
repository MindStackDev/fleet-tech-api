<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class StoreOperatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email:filter',
                'max:100',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:50',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^(\+?51)?\s?9\d{8}$/',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo del operador es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios (no se permiten números ni caracteres especiales como @ o símbolos).',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido con formato correcto (ejemplo@dominio.com).',
            'email.unique' => 'Este correo ya se encuentra registrado por otro usuario en la plataforma.',

            'password.required' => 'La contraseña de acceso es obligatoria.',
            'password.min' => 'La contraseña debe contener al menos 6 caracteres.',

            'phone.regex' => 'El teléfono debe ser un celular válido de 9 dígitos (ej. 987654321 o +51 987654321, sin letras ni símbolos).',
        ];
    }
}
