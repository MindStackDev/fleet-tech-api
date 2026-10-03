<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Inspections;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Inspections\StoreInspectionRequest;
use App\Http\Resources\Api\V1\Inspections\InspectionResource;
use App\Models\Inspection;
use App\Services\InspectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InspectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Inspection::query()->with([
            'vehicle.vehicleType',
            'operator',
            'receivingOperator',
        ]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', (int) $request->query('vehicle_id'));
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->query('shift'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $inspections = $query->orderByDesc('inspected_at')->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Historial de inspecciones y relevos recuperado.',
            'data' => InspectionResource::collection($inspections),
            'meta' => [
                'current_page' => $inspections->currentPage(),
                'last_page' => $inspections->lastPage(),
                'total' => $inspections->total(),
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $inspection = Inspection::with([
            'vehicle.vehicleType',
            'template',
            'operator',
            'receivingOperator',
            'answers.item',
            'answers.evidences',
            'evidences',
            'defects.item',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detalle de la inspección recuperado.',
            'data' => new InspectionResource($inspection),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function store(StoreInspectionRequest $request, InspectionService $service): JsonResponse
    {
        $inspection = $service->registerInspection($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Inspección de patio y relevo registrados exitosamente.',
            'data' => new InspectionResource($inspection),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_CREATED);
    }
}
