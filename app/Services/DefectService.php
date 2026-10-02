<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DefectSeverity;
use App\Enums\DefectStatus;
use App\Enums\VehicleStatus;
use App\Models\Defect;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

final class DefectService
{
    public function resolveDefect(
        Defect $defect,
        User $technician,
        string $resolutionNotes,
        ?string $resolvedEvidencePath = null
    ): Defect {
        return DB::transaction(function () use ($defect, $technician, $resolutionNotes, $resolvedEvidencePath): Defect {
            $defect->update([
                'status' => DefectStatus::RESOLVED,
                'resolved_by' => $technician->id,
                'resolution_notes' => $resolutionNotes,
                'resolved_evidence_path' => $resolvedEvidencePath,
                'resolved_at' => now(),
            ]);

            // Comprobamos si el vehículo ya no tiene más defectos críticos abiertos
            $remainingCriticalDefects = Defect::where('vehicle_id', $defect->vehicle_id)
                ->where('severity', DefectSeverity::CRITICAL)
                ->whereIn('status', [DefectStatus::OPEN, DefectStatus::IN_WORKSHOP])
                ->count();

            // Si ya no quedan defectos críticos, desbloqueamos la unidad en patio
            if ($remainingCriticalDefects === 0) {
                Vehicle::where('id', $defect->vehicle_id)
                    ->update(['status' => VehicleStatus::ACTIVE]);
            }

            return $defect->load(['vehicle', 'item', 'reporter', 'resolver']);
        });
    }
}
