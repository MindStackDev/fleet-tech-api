<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Fleet;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Fleet\VehicleTypeResource;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class VehicleTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = VehicleType::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Tipos de vehículo recuperados.',
            'data' => VehicleTypeResource::collection($types),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }
}
