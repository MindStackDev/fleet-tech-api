<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Fleet;

use App\Enums\VehicleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class StoreVehicleRequest extends FormRequest
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
            'company_id' => ['required', 'exists:companies,id'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'plate_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]{3}-?[A-Z0-9]{3}$/i',
                Rule::unique('vehicles', 'plate_number')->withoutTrashed(),
            ],
            'vin' => ['nullable', 'string', 'max:50'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'current_mileage' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', new Enum(VehicleStatus::class)],
            'specs' => ['nullable', 'array'],
        ];
    }
}
