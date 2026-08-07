<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\SolicitudTipo;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SolicitudTipoController extends Controller
{
    public function index()
    {
        return response()->json(SolicitudTipo::with('steps.rol')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_tipo' => 'required|string|max:45',
            'requiere_documento' => 'required|boolean',
            'requiere_valor' => 'required|boolean',
            'requiere_descripcion' => 'required|boolean',
            'steps' => 'nullable|array',
            'steps.*.rol_id' => 'required|string|exists:rol,uuid',
            'steps.*.orden' => 'required|integer',
            'steps.*.nombre_paso' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $tipo = SolicitudTipo::create([
                'name_tipo' => $request->name_tipo,
                'requiere_documento' => $request->requiere_documento,
                'requiere_valor' => $request->requiere_valor,
                'requiere_descripcion' => $request->requiere_descripcion,
                'activo' => true,
            ]);

            if ($request->has('steps') && is_array($request->steps)) {
                foreach ($request->steps as $stepData) {
                    $rol = Rol::where('uuid', $stepData['rol_id'])->firstOrFail();
                    WorkflowStep::create([
                        'solicitud_tipo_id' => $tipo->id_tipo,
                        'orden' => $stepData['orden'],
                        'rol_id' => $rol->id,
                        'nombre_paso' => $stepData['nombre_paso'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json($tipo->load('steps.rol'), 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Error al guardar el tipo de solicitud: '.$e->getMessage()], 500);
        }
    }

    public function show($uuid)
    {
        $tipo = SolicitudTipo::with('steps.rol')->where('uuid', $uuid)->first();

        if (! $tipo) {
            return response()->json(['message' => 'Tipo de solicitud no encontrado'], 404);
        }

        return response()->json($tipo);
    }

    public function update(Request $request, $uuid)
    {
        $tipo = SolicitudTipo::where('uuid', $uuid)->first();

        if (! $tipo) {
            return response()->json(['message' => 'Tipo de solicitud no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_tipo' => 'required|string|max:45',
            'requiere_documento' => 'required|boolean',
            'requiere_valor' => 'required|boolean',
            'requiere_descripcion' => 'required|boolean',
            'activo' => 'required|boolean',
            'steps' => 'nullable|array',
            'steps.*.rol_id' => 'required|string|exists:rol,uuid',
            'steps.*.orden' => 'required|integer',
            'steps.*.nombre_paso' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $tipo->update([
                'name_tipo' => $request->name_tipo,
                'requiere_documento' => $request->requiere_documento,
                'requiere_valor' => $request->requiere_valor,
                'requiere_descripcion' => $request->requiere_descripcion,
                'activo' => $request->activo,
            ]);

            // Sync steps
            WorkflowStep::where('solicitud_tipo_id', $tipo->id_tipo)->delete();

            if ($request->has('steps') && is_array($request->steps)) {
                foreach ($request->steps as $stepData) {
                    $rol = Rol::where('uuid', $stepData['rol_id'])->firstOrFail();
                    WorkflowStep::create([
                        'solicitud_tipo_id' => $tipo->id_tipo,
                        'orden' => $stepData['orden'],
                        'rol_id' => $rol->id,
                        'nombre_paso' => $stepData['nombre_paso'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json($tipo->load('steps.rol'));

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Error al actualizar el tipo de solicitud: '.$e->getMessage()], 500);
        }
    }

    public function destroy($uuid)
    {
        $tipo = SolicitudTipo::where('uuid', $uuid)->first();

        if (! $tipo) {
            return response()->json(['message' => 'Tipo de solicitud no encontrado'], 404);
        }

        try {
            DB::beginTransaction();

            // Set inactive instead of hard deleting to prevent breaking existing solicitudes
            $tipo->update(['activo' => false]);

            DB::commit();

            return response()->json(['message' => 'Tipo de solicitud desactivado exitosamente']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Error al desactivar el tipo de solicitud: '.$e->getMessage()], 500);
        }
    }
}
