<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreOperatorRequest;
use App\Http\Requests\Api\V1\Admin\UpdateOperatorRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

final class AdminOperatorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = User::query()
            ->with('company')
            ->where('role', UserRole::OPERATOR);

        if ($user->company_id) {
            $query->where('company_id', $user->company_id);
        }

        if ($request->filled('search')) {
            $search = (string) $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->query('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $operators = $query->withCount(['performedInspections', 'receivedInspections'])
            ->orderBy('name')
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Lista de operadores recuperada exitosamente.',
            'data' => $operators->items(),
            'meta' => [
                'current_page' => $operators->currentPage(),
                'last_page' => $operators->lastPage(),
                'total' => $operators->total(),
                'per_page' => $operators->perPage(),
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function store(StoreOperatorRequest $request): JsonResponse
    {
        $admin = $request->user();

        $operator = User::create([
            'company_id' => $admin->company_id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'phone' => $request->validated('phone'),
            'role' => UserRole::OPERATOR,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $operator->load('company');

        return response()->json([
            'success' => true,
            'message' => 'Operador registrado correctamente en el sistema.',
            'data' => new UserResource($operator),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $operator = User::with(['company', 'performedInspections.vehicle', 'receivedInspections.vehicle'])
            ->where('role', UserRole::OPERATOR)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detalle del operador obtenido correctamente.',
            'data' => $operator,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function update(UpdateOperatorRequest $request, int $id): JsonResponse
    {
        $operator = User::where('role', UserRole::OPERATOR)->findOrFail($id);

        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $operator->update($data);
        $operator->load('company');

        return response()->json([
            'success' => true,
            'message' => 'Datos del operador actualizados satisfactoriamente.',
            'data' => new UserResource($operator),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $operator = User::where('role', UserRole::OPERATOR)->findOrFail($id);
        $operator->is_active = !$operator->is_active;
        $operator->save();

        return response()->json([
            'success' => true,
            'message' => $operator->is_active ? 'Operador activado exitosamente.' : 'Operador desactivado exitosamente.',
            'data' => new UserResource($operator),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $operator = User::where('role', UserRole::OPERATOR)->findOrFail($id);
        
        // Desactivación preventiva para preservar integridad referencial de inspecciones
        $operator->is_active = false;
        $operator->save();

        return response()->json([
            'success' => true,
            'message' => 'Operador dado de baja del sistema.',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }
}
