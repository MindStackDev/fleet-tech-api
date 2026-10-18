<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== UserRole::ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Este módulo es exclusivo para administradores del sistema.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
