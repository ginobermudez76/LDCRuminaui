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
        if ($this->hasAccess($request)) {
            return $next($request);
        }

        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['error' => 'No autorizado. Debe iniciar sesión.'], 401);
        }

        $method = $request->method();
        $uri = $request->route() ? $request->route()->uri() : null;

        return response()->json([
            'error' => 'Acceso denegado. No tiene permisos para realizar esta acción.',
            'required_endpoint' => "{$method} {$uri}",
        ], 403);
    }

    private function hasAccess(Request $request): bool
    {
        $method = $request->method();
        $uri = $request->route() ? $request->route()->uri() : null;

        if (! $uri) {
            return true;
        }

        $endpoint = DB::table('endpoint')
            ->where('metodo', $method)
            ->where('url', $uri)
            ->where('deleted', false)
            ->first();

        if (! $endpoint || ! $endpoint->rbac_enabled) {
            return true;
        }

        $user = auth('api')->user();

        return $user && DB::table('rol_usuario')
            ->join('rol_opcion', 'rol_usuario.id_rol', '=', 'rol_opcion.id_rol')
            ->join('opcion_endpoint', 'rol_opcion.id_opcion', '=', 'opcion_endpoint.id_opcion')
            ->where('rol_usuario.id_usuario', $user->id)
            ->where('opcion_endpoint.id_endpoint', $endpoint->id)
            ->where('rol_usuario.deleted', false)
            ->where('rol_opcion.deleted', false)
            ->where('opcion_endpoint.deleted', false)
            ->exists();
    }
}
