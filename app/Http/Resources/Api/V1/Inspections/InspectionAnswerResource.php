<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Inspections;

use App\Models\InspectionAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InspectionAnswer
 */
final class InspectionAnswerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'checklist_item_id' => $this->checklist_item_id,
            'item_label' => $this->item?->label,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'value' => $this->value,
            'notes' => $this->notes,
            'evidences' => InspectionEvidenceResource::collection($this->whenLoaded('evidences')),
        ];
    }
}
