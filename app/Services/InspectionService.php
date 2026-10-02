<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AnswerStatus;
use App\Enums\DefectSeverity;
use App\Enums\DefectStatus;
use App\Enums\InspectionStatus;
use App\Enums\VehicleStatus;
use App\Models\ChecklistItem;
use App\Models\Defect;
use App\Models\Inspection;
use App\Models\InspectionAnswer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

final class InspectionService
{
    /**
     * @param array<string, mixed> $data
     */
    public function registerInspection(array $data, User $operator): Inspection
    {
        return DB::transaction(function () use ($data, $operator): Inspection {
            $hasFailures = false;

            // 1. Crear registro de inspección
            $inspection = Inspection::create([
                'vehicle_id' => $data['vehicle_id'],
                'checklist_template_id' => $data['checklist_template_id'],
                'inspection_type' => $data['inspection_type'],
                'shift' => $data['shift'],
                'operator_id' => $operator->id,
                'receiving_operator_id' => $data['receiving_operator_id'] ?? null,
                'start_mileage' => $data['start_mileage'],
                'end_mileage' => $data['end_mileage'] ?? null,
                'fuel_level' => $data['fuel_level'] ?? null,
                'yard_location' => $data['yard_location'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'status' => InspectionStatus::COMPLETED->value,
                'operator_signature_path' => $data['operator_signature_path'] ?? null,
                'receiving_signature_path' => $data['receiving_signature_path'] ?? null,
                'general_notes' => $data['general_notes'] ?? null,
                'inspected_at' => now(),
            ]);

            // 2. Actualizar kilometraje del vehículo si es mayor
            if ((int) $data['start_mileage'] > 0) {
                Vehicle::where('id', $data['vehicle_id'])
                    ->where('current_mileage', '<', $data['start_mileage'])
                    ->update(['current_mileage' => $data['start_mileage']]);
            }

            // 3. Procesar respuestas y evidencias
            foreach ($data['answers'] as $ans) {
                $answer = $inspection->answers()->create([
                    'checklist_item_id' => $ans['checklist_item_id'],
                    'status' => $ans['status'],
                    'value' => $ans['value'] ?? null,
                    'notes' => $ans['notes'] ?? null,
                ]);

                // Guardar fotos de evidencia de este ítem
                if (! empty($ans['evidences'])) {
                    foreach ($ans['evidences'] as $ev) {
                        $inspection->evidences()->create([
                            'inspection_answer_id' => $answer->id,
                            'file_path' => $ev['file_path'],
                            'caption' => $ev['caption'] ?? null,
                            'latitude' => $ev['latitude'] ?? null,
                            'longitude' => $ev['longitude'] ?? null,
                        ]);
                    }
                }

                // Si falló (ej. herramienta faltante o avería mecánica), genera defecto automático
                if ($ans['status'] === AnswerStatus::FAIL->value) {
                    $hasFailures = true;
                    $this->createDefectFromAnswer($inspection, $answer, $operator);
                }
            }

            // 4. Guardar evidencias generales del vehículo (ej. fotos panorámicas de patio)
            if (! empty($data['general_evidences'])) {
                foreach ($data['general_evidences'] as $genEv) {
                    $inspection->evidences()->create([
                        'inspection_answer_id' => null,
                        'file_path' => $genEv['file_path'],
                        'caption' => $genEv['caption'] ?? 'Foto general de la unidad en patio',
                        'latitude' => $genEv['latitude'] ?? null,
                        'longitude' => $genEv['longitude'] ?? null,
                    ]);
                }
            }

            // 5. Si hubo fallas, la inspección queda marcada como FLAGGED
            if ($hasFailures) {
                $inspection->update(['status' => InspectionStatus::FLAGGED->value]);
            }

            return $inspection->load([
                'vehicle.vehicleType',
                'answers.item',
                'answers.evidences',
                'evidences',
                'defects.vehicle',
                'defects.item',
                'defects.reporter',
                'operator',
                'receivingOperator',
            ]);
        });
    }

    private function createDefectFromAnswer(Inspection $inspection, InspectionAnswer $answer, User $reporter): Defect
    {
        $item = $answer->item ?? ChecklistItem::findOrFail($answer->checklist_item_id);
        $severity = $this->determineSeverity($item);

        $defect = Defect::create([
            'inspection_id' => $inspection->id,
            'checklist_item_id' => $item->id,
            'vehicle_id' => $inspection->vehicle_id,
            'severity' => $severity,
            'status' => DefectStatus::OPEN,
            'description' => "Falla reportada en patio: {$item->label}. Observación: " . ($answer->notes ?? 'Sin notas adicionales.'),
            'reported_by' => $reporter->id,
        ]);

        // Bloqueo preventivo de la unidad si el defecto es crítico
        if ($severity === DefectSeverity::CRITICAL) {
            Vehicle::where('id', $inspection->vehicle_id)
                ->update(['status' => VehicleStatus::IN_MAINTENANCE]);
        }

        return $defect;
    }

    private function determineSeverity(ChecklistItem $item): DefectSeverity
    {
        $label = mb_strtolower($item->label);

        if (str_contains($label, 'freno') || str_contains($label, 'fuga') || str_contains($label, 'crítico') || str_contains($label, 'extintor')) {
            return DefectSeverity::CRITICAL;
        }

        if (str_contains($label, 'gata') || str_contains($label, 'llave') || str_contains($label, 'neumático') || str_contains($label, 'luz')) {
            return DefectSeverity::MEDIUM;
        }

        return DefectSeverity::LOW;
    }
}
