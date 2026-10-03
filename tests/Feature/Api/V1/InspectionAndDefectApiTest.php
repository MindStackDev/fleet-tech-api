<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\DefectSeverity;
use App\Enums\DefectStatus;
use App\Enums\VehicleStatus;
use App\Models\ChecklistItem;
use App\Models\ChecklistTemplate;
use App\Models\Defect;
use App\Models\User;
use App\Models\Vehicle;
use Tests\TestCase;

final class InspectionAndDefectApiTest extends TestCase
{
    public function test_inspection_registration_creates_defects_and_locks_vehicle_on_critical_failure(): void
    {
        $operadorSaliente = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();
        $operadorEntrante = User::where('email', 'operador.tarde@fleettech.pe')->firstOrFail();
        $vehicle = Vehicle::where('plate_number', 'ABC-101')->firstOrFail();
        $template = ChecklistTemplate::with('sections.items')->firstOrFail();

        $frenoItem = ChecklistItem::where('label', 'like', '%Frenos%')->firstOrFail();
        $gataItem = ChecklistItem::where('label', 'like', '%Gata%')->firstOrFail();

        $payload = [
            'vehicle_id' => $vehicle->id,
            'checklist_template_id' => $template->id,
            'inspection_type' => 'shift_handover',
            'shift' => 'morning',
            'receiving_operator_id' => $operadorEntrante->id,
            'start_mileage' => 45500,
            'fuel_level' => '75%',
            'yard_location' => 'Patio Norte - Bahía 04',
            'latitude' => -12.046374,
            'longitude' => -77.042793,
            'operator_signature_path' => 'signatures/operador_juan.png',
            'receiving_signature_path' => 'signatures/operador_mario.png',
            'general_notes' => 'Relevo de turno mañana a tarde. Se detectó fuga de aire en frenos traseros y gata incompleta.',
            'answers' => [
                [
                    'checklist_item_id' => $frenoItem->id,
                    'status' => 'fail',
                    'notes' => 'Fuga de aire en línea neumática de freno de parqueo.',
                    'evidences' => [
                        [
                            'file_path' => 'evidences/fuga_aire_freno.jpg',
                            'caption' => 'Manguera neumática cuarteada con escape visible.',
                        ],
                    ],
                ],
                [
                    'checklist_item_id' => $gataItem->id,
                    'status' => 'fail',
                    'notes' => 'Gata hidráulica sin palanca extensora.',
                    'evidences' => [
                        [
                            'file_path' => 'evidences/gata_sin_palanca.jpg',
                            'caption' => 'Falta palanca metálica en el kit de cabina.',
                        ],
                    ],
                ],
            ],
            'general_evidences' => [
                [
                    'file_path' => 'evidences/panoramica_frontal_camion.jpg',
                    'caption' => 'Foto frontal de la unidad en bahía al entregar turno',
                ],
            ],
        ];

        $response = $this->actingAs($operadorSaliente)
            ->postJson('/api/v1/inspections', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'flagged');

        // 1. Verificamos que se crearon los defectos
        $this->assertDatabaseHas('defects', [
            'vehicle_id' => $vehicle->id,
            'checklist_item_id' => $frenoItem->id,
            'severity' => DefectSeverity::CRITICAL->value,
            'status' => DefectStatus::OPEN->value,
        ]);

        $this->assertDatabaseHas('defects', [
            'vehicle_id' => $vehicle->id,
            'checklist_item_id' => $gataItem->id,
            'status' => DefectStatus::OPEN->value,
        ]);

        // 2. Verificamos que el vehículo fue BLOQUEADO automáticamente a 'in_maintenance'
        $vehicle->refresh();
        $this->assertEquals(VehicleStatus::IN_MAINTENANCE, $vehicle->status);
        $this->assertEquals(45500, $vehicle->current_mileage);
    }

    public function test_technician_can_resolve_defect_and_unlock_vehicle(): void
    {
        $mecanico = User::where('email', 'mecanico@fleettech.pe')->firstOrFail();
        $operador = User::where('email', 'operador.manana@fleettech.pe')->firstOrFail();
        $vehicle = Vehicle::where('plate_number', 'ABC-101')->firstOrFail();
        $frenoItem = ChecklistItem::where('label', 'like', '%Frenos%')->firstOrFail();
        $template = ChecklistTemplate::firstOrFail();

        // Simulamos una inspección previa con falla crítica que bloqueó la unidad
        $inspection = $operador->performedInspections()->create([
            'vehicle_id' => $vehicle->id,
            'checklist_template_id' => $template->id,
            'inspection_type' => 'routine_check',
            'shift' => 'morning',
            'start_mileage' => 45200,
            'status' => 'flagged',
            'inspected_at' => now(),
        ]);

        $criticalDefect = Defect::create([
            'inspection_id' => $inspection->id,
            'checklist_item_id' => $frenoItem->id,
            'vehicle_id' => $vehicle->id,
            'severity' => DefectSeverity::CRITICAL,
            'status' => DefectStatus::OPEN,
            'description' => 'Manguera neumática cuarteada.',
            'reported_by' => $operador->id,
        ]);

        // La unidad se encuentra bloqueada
        $vehicle->update(['status' => VehicleStatus::IN_MAINTENANCE]);

        // Técnico resuelve el defecto
        $response = $this->actingAs($mecanico)
            ->patchJson("/api/v1/defects/{$criticalDefect->id}/resolve", [
                'resolution_notes' => 'Se reemplazó manguera neumática de freno y se purgó el sistema. Presión sostenida a 120 PSI.',
                'resolved_evidence_path' => 'evidences/reparacion_freno_ok.jpg',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'resolved');

        // Al no quedar más defectos críticos, la unidad se desbloquea automáticamente a 'active'
        $vehicle->refresh();
        $this->assertEquals(VehicleStatus::ACTIVE, $vehicle->status);
    }
}
