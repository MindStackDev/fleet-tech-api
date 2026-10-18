<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\DefectStatus;
use App\Enums\UserRole;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Models\Defect;
use App\Models\Inspection;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDashboardController extends Controller
{
    public function dailyActivity(Request $request): JsonResponse
    {
        $targetDate = $request->filled('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : Carbon::today();

        $endOfDay = (clone $targetDate)->endOfDay();

        // 1. Inspecciones y Relevos del da
        $inspections = Inspection::with(['vehicle.vehicleType', 'operator', 'receivingOperator'])
            ->whereBetween('inspected_at', [$targetDate, $endOfDay])
            ->orWhere(function ($query) use ($targetDate, $endOfDay) {
                $query->whereNull('inspected_at')
                      ->whereBetween('created_at', [$targetDate, $endOfDay]);
            })
            ->orderByDesc('inspected_at')
            ->get();

        // 2. Defectos reportados en el da
        $reportedDefects = Defect::with(['vehicle', 'reporter', 'item'])
            ->whereBetween('created_at', [$targetDate, $endOfDay])
            ->orderByDesc('created_at')
            ->get();

        // 3. Defectos resueltos en el da
        $resolvedDefects = Defect::with(['vehicle', 'resolver', 'item'])
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$targetDate, $endOfDay])
            ->orderByDesc('resolved_at')
            ->get();

        // 4. Mtricas Generales del Da y Estado de Operaciones
        $totalInspectionsToday = $inspections->count();
        $totalActiveOperators = User::where('role', UserRole::OPERATOR)->where('is_active', true)->count();
        $operatorsActiveToday = $inspections->pluck('operator_id')->merge($inspections->pluck('receiving_operator_id'))->filter()->unique()->count();
        
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', VehicleStatus::ACTIVE)->count();
        $maintenanceVehicles = Vehicle::where('status', VehicleStatus::IN_MAINTENANCE)->count();
        $outOfServiceVehicles = Vehicle::where('status', VehicleStatus::OUT_OF_SERVICE)->count();

        $openDefectsCount = Defect::whereIn('status', [DefectStatus::OPEN, DefectStatus::IN_WORKSHOP])->count();
        $criticalDefectsToday = $reportedDefects->where('severity.value', 'critical')->count();

        // 5. Timeline unificada de eventos cronolgicos
        $timeline = [];

        foreach ($inspections as $insp) {
            $timestamp = $insp->inspected_at ?? $insp->created_at;
            $timeline[] = [
                'id' => 'insp-' . $insp->id,
                'type' => 'inspection',
                'title' => 'Inspeccin / Relevo Registrado',
                'description' => "Unidad {$insp->vehicle?->plate_number} ({$insp->vehicle?->brand} {$insp->vehicle?->model}) - Turno {$insp->shift?->label()}",
                'operator_name' => $insp->operator?->name ?? 'Operador no asignado',
                'receiving_operator_name' => $insp->receivingOperator?->name,
                'vehicle_plate' => $insp->vehicle?->plate_number,
                'status' => $insp->status?->value ?? 'completed',
                'severity' => $insp->status?->value === 'critical' ? 'critical' : 'normal',
                'timestamp' => $timestamp ? $timestamp->toIso8601String() : null,
                'time_formatted' => $timestamp ? $timestamp->format('H:i') : '--:--',
                'raw_timestamp' => $timestamp ? $timestamp->timestamp : 0,
            ];
        }

        foreach ($reportedDefects as $defect) {
            $timestamp = $defect->created_at;
            $timeline[] = [
                'id' => 'defect-rep-' . $defect->id,
                'type' => 'defect_reported',
                'title' => 'Falla / Defecto Reportado',
                'description' => ($defect->item?->label ?? 'Falla mecánica') . ": " . $defect->description,
                'operator_name' => $defect->reporter?->name ?? 'Reportado en patio',
                'vehicle_plate' => $defect->vehicle?->plate_number,
                'status' => $defect->status?->value,
                'severity' => $defect->severity?->value ?? 'medium',
                'timestamp' => $timestamp ? $timestamp->toIso8601String() : null,
                'time_formatted' => $timestamp ? $timestamp->format('H:i') : '--:--',
                'raw_timestamp' => $timestamp ? $timestamp->timestamp : 0,
            ];
        }

        foreach ($resolvedDefects as $resolved) {
            $timestamp = $resolved->resolved_at;
            $timeline[] = [
                'id' => 'defect-res-' . $resolved->id,
                'type' => 'defect_resolved',
                'title' => 'Avería Resuelta en Taller',
                'description' => "Unidad {$resolved->vehicle?->plate_number}: " . ($resolved->resolution_notes ?? 'Defecto subsanado'),
                'operator_name' => $resolved->resolver?->name ?? 'Técnico mecánico',
                'vehicle_plate' => $resolved->vehicle?->plate_number,
                'status' => 'resolved',
                'severity' => 'resolved',
                'timestamp' => $timestamp ? $timestamp->toIso8601String() : null,
                'time_formatted' => $timestamp ? $timestamp->format('H:i') : '--:--',
                'raw_timestamp' => $timestamp ? $timestamp->timestamp : 0,
            ];
        }

        // Ordenar descendentemente por timestamp
        usort($timeline, fn($a, $b) => $b['raw_timestamp'] <=> $a['raw_timestamp']);

        return response()->json([
            'success' => true,
            'message' => 'Resumen y bitácora de actividad del día recuperados.',
            'data' => [
                'date' => $targetDate->format('Y-m-d'),
                'date_human' => $targetDate->translatedFormat('l, d \d\e F \d\e Y'),
                'metrics' => [
                    'inspections_today' => $totalInspectionsToday,
                    'operators_active_today' => $operatorsActiveToday,
                    'total_operators' => $totalActiveOperators,
                    'open_defects' => $openDefectsCount,
                    'critical_defects_today' => $criticalDefectsToday,
                    'fleet' => [
                        'total' => $totalVehicles,
                        'active' => $activeVehicles,
                        'maintenance' => $maintenanceVehicles,
                        'out_of_service' => $outOfServiceVehicles,
                    ],
                ],
                'timeline' => $timeline,
            ],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }
}
