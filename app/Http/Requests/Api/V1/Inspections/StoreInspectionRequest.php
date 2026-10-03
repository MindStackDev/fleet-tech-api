<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Inspections;

use App\Enums\AnswerStatus;
use App\Enums\InspectionType;
use App\Enums\ShiftType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class StoreInspectionRequest extends FormRequest
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
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'checklist_template_id' => ['required', 'exists:checklist_templates,id'],
            'inspection_type' => ['required', new Enum(InspectionType::class)],
            'shift' => ['required', new Enum(ShiftType::class)],
            'receiving_operator_id' => ['nullable', 'exists:users,id'],
            'start_mileage' => ['required', 'integer', 'min:0'],
            'end_mileage' => ['nullable', 'integer', 'gte:start_mileage'],
            'fuel_level' => ['nullable', 'string', 'max:20'],
            'yard_location' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'operator_signature_path' => ['nullable', 'string'],
            'receiving_signature_path' => ['nullable', 'string'],
            'general_notes' => ['nullable', 'string'],
            
            // Respuestas
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.checklist_item_id' => ['required', 'exists:checklist_items,id'],
            'answers.*.status' => ['required', new Enum(AnswerStatus::class)],
            'answers.*.value' => ['nullable', 'string'],
            'answers.*.notes' => ['nullable', 'string'],
            'answers.*.evidences' => ['nullable', 'array'],
            'answers.*.evidences.*.file_path' => ['required_with:answers.*.evidences', 'string'],
            'answers.*.evidences.*.caption' => ['nullable', 'string', 'max:255'],
            'answers.*.evidences.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'answers.*.evidences.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // Evidencias generales
            'general_evidences' => ['nullable', 'array'],
            'general_evidences.*.file_path' => ['required_with:general_evidences', 'string'],
            'general_evidences.*.caption' => ['nullable', 'string', 'max:255'],
            'general_evidences.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'general_evidences.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
