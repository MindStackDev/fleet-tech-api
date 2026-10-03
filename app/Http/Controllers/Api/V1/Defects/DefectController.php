<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Defects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Defects\ResolveDefectRequest;
use App\Http\Resources\Api\V1\Defects\DefectResource;
use App\Models\Defect;
use App\Services\DefectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class DefectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Defect::query()->with([
            'vehicle',
            'item',
            'reporter',
            'resolver',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->query('severity'));
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', (int) $request->query('vehicle_id'));
        }

        $defects = $query->orderByDesc('created_at')->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Lista de defectos y novedades de taller recuperada.',
            'data' => DefectResource::collection($defects),
            'meta' => [
                'current_page' => $defects->currentPage(),
                'last_page' => $defects->lastPage(),
                'total' => $defects->total(),
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $defect = Defect::with([
            'vehicle',
            'item',
            'reporter',
            'resolver',
            'inspection',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detalle del defecto recuperado.',
            'data' => new DefectResource($defect),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function resolve(int $id, ResolveDefectRequest $request, DefectService $service): JsonResponse
    {
        $defect = Defect::findOrFail($id);

        $resolved = $service->resolveDefect(
            defect: $defect,
            technician: $request->user(),
            resolutionNotes: (string) $request->validated('resolution_notes'),
            resolvedEvidencePath: $request->validated('resolved_evidence_path')
        );

        return response()->json([
            'success' => true,
            'message' => 'Defecto subsanado exitosamente. Se evaluó el desbloqueo de la unidad.',
            'data' => new DefectResource($resolved),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }
}
