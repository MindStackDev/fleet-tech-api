<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'role_label' => $this->role->label(),
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
                'tax_id' => $this->company?->tax_id,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
