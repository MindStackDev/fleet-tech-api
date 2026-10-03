<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Inspections;

use App\Http\Resources\Api\V1\Defects\DefectResource;
use App\Http\Resources\Api\V1\Fleet\VehicleResource;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Inspection
 */
final class InspectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inspection_type' => $this->inspection_type->value,
            'inspection_type_label' => $this->inspection_type->label(),
            'shift' => $this->shift->value,
            'shift_label' => $this->shift->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'start_mileage' => $this->start_mileage,
            'end_mileage' => $this->end_mileage,
            'fuel_level' => $this->fuel_level,
            'yard_location' => $this->yard_location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'operator' => [
                'id' => $this->operator?->id,
                'name' => $this->operator?->name,
            ],
            'receiving_operator' => $this->when($this->receiving_operator_id !== null, fn() => [
                'id' => $this->receivingOperator?->id,
                'name' => $this->receivingOperator?->name,
            ]),
            'operator_signature_path' => $this->operator_signature_path,
            'receiving_signature_path' => $this->receiving_signature_path,
            'general_notes' => $this->general_notes,
            'inspected_at' => $this->inspected_at?->toIso8601String(),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'answers' => InspectionAnswerResource::collection($this->whenLoaded('answers')),
            'evidences' => InspectionEvidenceResource::collection($this->whenLoaded('evidences')),
            'defects' => DefectResource::collection($this->whenLoaded('defects')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
