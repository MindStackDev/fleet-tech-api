<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Fleet;

use App\Http\Resources\Api\V1\Defects\DefectResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vehicle
 */
final class VehicleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plate_number' => $this->plate_number,
            'vin' => $this->vin,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'current_mileage' => $this->current_mileage,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'specs' => $this->specs,
            'vehicle_type' => [
                'id' => $this->vehicleType?->id,
                'name' => $this->vehicleType?->name,
            ],
            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
            ],
            'open_defects_count' => $this->whenCounted('defects'),
            'open_defects' => DefectResource::collection($this->whenLoaded('defects')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
