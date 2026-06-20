<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RbacMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $method = $request->method();
        // Route URI template (e.g. "api/solicitudes/{id}")
        $uri = $request->route() ? $request->route()->uri() : null;

        if (!$uri) {
            return $next($request);
        }

        // 1. Check if the endpoint exists in the database and has RBAC enabled
        $endpoint = DB::table('endpoint')
            ->where('metodo', $method)
            ->where('url', $uri)
            ->where('deleted', false)
            ->first();

        // If the endpoint is not registered or has rbac_enabled set to false, allow access
        if (!$endpoint || !$endpoint->rbac_enabled) {
            return $next($request);
        }

        // 2. Check if the user is authenticated via JWT guard
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'No autorizado. Debe iniciar sesión.'], 401);
        }

        // 3. Verify if any of the user's active roles are connected to this endpoint through options
        $hasPermission = DB::table('rol_usuario')
            ->join('rol_opcion', 'rol_usuario.id_rol', '=', 'rol_opcion.id_rol')
            ->join('opcion_endpoint', 'rol_opcion.id_opcion', '=', 'opcion_endpoint.id_opcion')
            ->where('rol_usuario.id_usuario', $user->id)
            ->where('opcion_endpoint.id_endpoint', $endpoint->id)
            ->where('rol_usuario.deleted', false)
            ->where('rol_opcion.deleted', false)
            ->where('opcion_endpoint.deleted', false)
            ->exists();

        if ($hasPermission) {
            return $next($request);
        }

        return response()->json([
            'error' => 'Acceso denegado. No tiene permisos para realizar esta acción.',
            'required_endpoint' => "{$method} {$uri}"
        ], 403);
    }
}
