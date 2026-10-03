<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Defects;

use App\Models\Defect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Defect
 */
final class DefectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inspection_id' => $this->inspection_id,
            'vehicle_id' => $this->vehicle_id,
            'plate_number' => $this->vehicle?->plate_number,
            'item_label' => $this->item?->label,
            'severity' => $this->severity->value,
            'severity_label' => $this->severity->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'description' => $this->description,
            'reported_by' => [
                'id' => $this->reporter?->id,
                'name' => $this->reporter?->name,
            ],
            'resolved_by' => $this->when($this->resolved_by !== null, fn() => [
                'id' => $this->resolver?->id,
                'name' => $this->resolver?->name,
            ]),
            'resolution_notes' => $this->resolution_notes,
            'resolved_evidence_path' => $this->resolved_evidence_path,
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
