<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Defects;

use Illuminate\Foundation\Http\FormRequest;

final class ResolveDefectRequest extends FormRequest
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
            'resolution_notes' => ['required', 'string', 'min:5', 'max:1000'],
            'resolved_evidence_path' => ['nullable', 'string'],
        ];
    }
}
