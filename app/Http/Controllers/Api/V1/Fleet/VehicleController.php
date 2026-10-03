<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Fleet;

use App\Enums\DefectStatus;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Fleet\StoreVehicleRequest;
use App\Http\Resources\Api\V1\Fleet\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class VehicleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::query()->with(['vehicleType', 'company']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'ilike', "%{$search}%")
                  ->orWhere('brand', 'ilike', "%{$search}%")
                  ->orWhere('model', 'ilike', "%{$search}%");
            });
        }

        $vehicles = $query->orderBy('plate_number')->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Lista de unidades en patio recuperada.',
            'data' => VehicleResource::collection($vehicles),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'total' => $vehicles->total(),
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $vehicle = Vehicle::with([
            'vehicleType',
            'company',
            'defects' => fn($q) => $q->whereIn('status', [DefectStatus::OPEN, DefectStatus::IN_WORKSHOP])
                ->with(['reporter', 'item']),
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detalle de la unidad recuperado.',
            'data' => new VehicleResource($vehicle),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $vehicle = Vehicle::create([
            'company_id' => $data['company_id'],
            'vehicle_type_id' => $data['vehicle_type_id'],
            'plate_number' => strtoupper((string) $data['plate_number']),
            'vin' => isset($data['vin']) ? strtoupper((string) $data['vin']) : null,
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => $data['year'] ?? null,
            'current_mileage' => $data['current_mileage'] ?? 0,
            'status' => isset($data['status']) ? VehicleStatus::from($data['status']) : VehicleStatus::ACTIVE,
            'specs' => $data['specs'] ?? null,
        ]);

        $vehicle->load(['vehicleType', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Unidad registrada exitosamente.',
            'data' => new VehicleResource($vehicle),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_CREATED);
    }
}
