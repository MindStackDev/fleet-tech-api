<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Checklists;

use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ChecklistItem
 */
final class ChecklistItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'description' => $this->description,
            'input_type' => $this->input_type->value,
            'input_type_label' => $this->input_type->label(),
            'options' => $this->options,
            'is_mandatory' => $this->is_mandatory,
            'requires_photo_on_fail' => $this->requires_photo_on_fail,
            'order_index' => $this->order_index,
        ];
    }
}
