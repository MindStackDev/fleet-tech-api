<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateOperatorRequest extends FormRequest
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
        $operatorId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s]+$/u',
            ],
            'email' => [
                'sometimes',
                'string',
                'email:filter',
                'max:100',
                Rule::unique('users', 'email')->ignore($operatorId),
            ],
            'password' => [
                'nullable',
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
                'sometimes',
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
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios (sin números ni caracteres especiales como @ o símbolos).',

            'email.email' => 'Debes ingresar un correo electrónico válido con formato correcto (ejemplo@dominio.com).',
            'email.unique' => 'Este correo ya se encuentra registrado por otro usuario.',

            'password.min' => 'La contraseña debe contener al menos 6 caracteres.',

            'phone.regex' => 'El teléfono debe ser un celular válido de 9 dígitos (ej. 987654321 o +51 987654321, sin letras ni símbolos).',
        ];
    }
}
