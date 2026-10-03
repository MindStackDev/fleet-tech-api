<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Checklists;

use App\Models\ChecklistTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ChecklistTemplate
 */
final class ChecklistTemplateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'shift_type' => $this->shift_type->value,
            'shift_type_label' => $this->shift_type->label(),
            'version' => $this->version,
            'is_active' => $this->is_active,
            'vehicle_type' => [
                'id' => $this->vehicleType?->id,
                'name' => $this->vehicleType?->name,
            ],
            'sections' => ChecklistSectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
