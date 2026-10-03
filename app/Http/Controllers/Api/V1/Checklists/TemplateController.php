<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Checklists;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Checklists\ChecklistTemplateResource;
use App\Models\ChecklistTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class TemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $templates = ChecklistTemplate::query()
            ->where('is_active', true)
            ->with(['vehicleType'])
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Plantillas de checklist recuperadas.',
            'data' => ChecklistTemplateResource::collection($templates),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $template = ChecklistTemplate::with([
            'vehicleType',
            'sections.items',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Estructura completa de la plantilla recuperada.',
            'data' => new ChecklistTemplateResource($template),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }
}
