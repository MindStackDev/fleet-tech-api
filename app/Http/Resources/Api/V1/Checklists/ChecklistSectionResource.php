<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Checklists;

use App\Models\ChecklistSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ChecklistSection
 */
final class ChecklistSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'order_index' => $this->order_index,
            'items' => ChecklistItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
