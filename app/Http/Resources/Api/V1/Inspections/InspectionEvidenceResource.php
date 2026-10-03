<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Inspections;

use App\Models\InspectionEvidence;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InspectionEvidence
 */
final class InspectionEvidenceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inspection_answer_id' => $this->inspection_answer_id,
            'file_path' => $this->file_path,
            'caption' => $this->caption,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
