<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Opcion;
use App\Models\Endpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RbacAdminController extends Controller
{
    // ==========================================
    // ROLES MANAGEMENT
    // ==========================================

    public function listRoles()
    {
        // Load active roles with active options
        $roles = Rol::where('deleted', false)
            ->with(['opciones' => function ($query) {
                $query->where('opcion.deleted', false)
                      ->where('rol_opcion.deleted', false);
            }])
            ->get();

        return response()->json($roles);
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:100|unique:rol,codigo',
            'nombre_rol' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $role = new Rol();
        $role->uuid = (string) Str::uuid();
        $role->codigo = strtoupper(trim($validated['codigo']));
        $role->nombre_rol = trim($validated['nombre_rol']);
        $role->descripcion = isset($validated['descripcion']) ? trim($validated['descripcion']) : null;
        $role->deleted = false;
        $role->save();

        return response()->json($role, 201);
    }

    public function updateRole(Request $request, $id)
    {
        $role = Rol::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|string|max:100|unique:rol,codigo,' . $role->id,
            'nombre_rol' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $role->codigo = strtoupper(trim($validated['codigo']));
        $role->nombre_rol = trim($validated['nombre_rol']);
        $role->descripcion = isset($validated['descripcion']) ? trim($validated['descripcion']) : null;
        $role->save();

        return response()->json($role);
    }

    public function destroyRole($id)
    {
        $role = Rol::findOrFail($id);

        // System essential roles should not be deleted (roles 1-9)
        if ($role->id <= 9) {
            return response()->json(['error' => 'No se puede eliminar un rol del sistema esencial.'], 400);
        }

        $role->deleted = true;
        $role->deleted_at = now();
        $role->save();

        return response()->json(['message' => 'Rol eliminado con éxito.']);
    }

    public function syncRoleOptions(Request $request, $id)
    {
        $role = Rol::findOrFail($id);
        $optionIds = $request->input('option_ids', []);

        try {
            DB::beginTransaction();

            $existingPivots = DB::table('rol_opcion')
                ->where('id_rol', $role->id)
                ->get();

            $existingOptionIds = $existingPivots->pluck('id_opcion')->toArray();

            foreach ($optionIds as $optId) {
                if (in_array($optId, $existingOptionIds)) {
                    DB::table('rol_opcion')
                        ->where('id_rol', $role->id)
                        ->where('id_opcion', $optId)
                        ->update([
                            'deleted' => false,
                            'deleted_at' => null,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('rol_opcion')->insert([
                        'uuid' => (string) Str::uuid(),
                        'id_rol' => $role->id,
                        'id_opcion' => $optId,
                        'deleted' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Soft-delete pivots not present in the new set
            DB::table('rol_opcion')
                ->where('id_rol', $role->id)
                ->whereNotIn('id_opcion', $optionIds)
                ->update([
                    'deleted' => true,
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json(['message' => 'Menús asociados actualizados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al sincronizar opciones: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // OPTIONS MANAGEMENT
    // ==========================================

    public function listOptions()
    {
        $options = Opcion::where('deleted', false)
            ->with(['endpoints' => function ($query) {
                $query->where('endpoint.deleted', false)
                      ->where('opcion_endpoint.deleted', false);
            }])
            ->get();

        return response()->json($options);
    }

    public function storeOption(Request $request)
    {
        $validated = $request->validate([
            'nombre_opcion' => 'required|string|max:150|unique:opcion,nombre_opcion',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $option = new Opcion();
        $option->uuid = (string) Str::uuid();
        $option->nombre_opcion = strtoupper(trim($validated['nombre_opcion']));
        $option->descripcion = isset($validated['descripcion']) ? trim($validated['descripcion']) : null;
        $option->deleted = false;
        $option->save();

        return response()->json($option, 201);
    }

    public function updateOption(Request $request, $id)
    {
        $option = Opcion::findOrFail($id);

        $validated = $request->validate([
            'nombre_opcion' => 'required|string|max:150|unique:opcion,nombre_opcion,' . $option->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $option->nombre_opcion = strtoupper(trim($validated['nombre_opcion']));
        $option->descripcion = isset($validated['descripcion']) ? trim($validated['descripcion']) : null;
        $option->save();

        return response()->json($option);
    }

    public function destroyOption($id)
    {
        $option = Opcion::findOrFail($id);

        // Do not delete basic system options
        $essentialOptions = ['G_SOLICITUDES_PROPIAS', 'REGISTRAR_USUARIOS', 'G_SOLICITUDES_ASIGNADAS', 'APROBAR_SOLICITUDES', 'PUBLICAR_CONTENIDO', 'CONFIGURAR_RBAC'];
        if (in_array($option->nombre_opcion, $essentialOptions)) {
            return response()->json(['error' => 'No se puede eliminar un menú de sistema predeterminado.'], 400);
        }

        $option->deleted = true;
        $option->deleted_at = now();
        $option->save();

        return response()->json(['message' => 'Opción de menú eliminada con éxito.']);
    }

    public function syncOptionEndpoints(Request $request, $id)
    {
        $option = Opcion::findOrFail($id);
        $endpointIds = $request->input('endpoint_ids', []);

        try {
            DB::beginTransaction();

            $existingPivots = DB::table('opcion_endpoint')
                ->where('id_opcion', $option->id)
                ->get();

            $existingEndpointIds = $existingPivots->pluck('id_endpoint')->toArray();

            foreach ($endpointIds as $endId) {
                if (in_array($endId, $existingEndpointIds)) {
                    DB::table('opcion_endpoint')
                        ->where('id_opcion', $option->id)
                        ->where('id_endpoint', $endId)
                        ->update([
                            'deleted' => false,
                            'deleted_at' => null,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('opcion_endpoint')->insert([
                        'uuid' => (string) Str::uuid(),
                        'id_opcion' => $option->id,
                        'id_endpoint' => $endId,
                        'deleted' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Soft-delete pivots not present in the new set
            DB::table('opcion_endpoint')
                ->where('id_opcion', $option->id)
                ->whereNotIn('id_endpoint', $endpointIds)
                ->update([
                    'deleted' => true,
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json(['message' => 'Endpoints y permisos asociados actualizados.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al asociar permisos: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // ENDPOINTS MANAGEMENT
    // ==========================================

    public function listEndpoints()
    {
        $endpoints = Endpoint::where('deleted', false)->get();
        return response()->json($endpoints);
    }

    public function storeEndpoint(Request $request)
    {
        $validated = $request->validate([
            'nombre_endpoint' => 'required|string|max:150',
            'metodo' => 'required|string|max:15',
            'url' => 'required|string|max:255',
            'rbac_enabled' => 'required|boolean',
        ]);

        $endpoint = new Endpoint();
        $endpoint->uuid = (string) Str::uuid();
        $endpoint->nombre_endpoint = trim($validated['nombre_endpoint']);
        $endpoint->metodo = strtoupper(trim($validated['metodo']));
        $endpoint->url = trim($validated['url']);
        $endpoint->rbac_enabled = (bool) $validated['rbac_enabled'];
        $endpoint->deleted = false;
        $endpoint->save();

        return response()->json($endpoint, 201);
    }

    public function updateEndpoint(Request $request, $id)
    {
        $endpoint = Endpoint::findOrFail($id);

        $validated = $request->validate([
            'nombre_endpoint' => 'required|string|max:150',
            'metodo' => 'required|string|max:15',
            'url' => 'required|string|max:255',
            'rbac_enabled' => 'required|boolean',
        ]);

        $endpoint->nombre_endpoint = trim($validated['nombre_endpoint']);
        $endpoint->metodo = strtoupper(trim($validated['metodo']));
        $endpoint->url = trim($validated['url']);
        $endpoint->rbac_enabled = (bool) $validated['rbac_enabled'];
        $endpoint->save();

        return response()->json($endpoint);
    }

    public function destroyEndpoint($id)
    {
        $endpoint = Endpoint::findOrFail($id);

        // Do not delete RBAC configuration endpoints
        if (strpos($endpoint->url, 'api/rbac') !== false) {
            return response()->json(['error' => 'No se puede eliminar un permiso de administración del sistema.'], 400);
        }

        $endpoint->deleted = true;
        $endpoint->deleted_at = now();
        $endpoint->save();

        return response()->json(['message' => 'Endpoint y permiso eliminado con éxito.']);
    }
}
