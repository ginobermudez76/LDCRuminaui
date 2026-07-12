<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SolicitudController extends Controller
{
    /**
     * Display a listing of requests created by the authenticated user.
     * If the user is an admin or president, list all requests.
     */
    public function index()
    {
        $user = auth('api')->user();

        // Admin (has REGISTRAR_USUARIOS option) or Presidente (codigo 'PRESIDENTE') sees all
        if ($user->roles->contains('codigo', 'PRESIDENTE') || $user->hasOption('REGISTRAR_USUARIOS')) {
            $solicitudes = Solicitud::with(['tipoRelation', 'solicitanteRelation', 'encargadoRelation', 'solicitantextRelation', 'estadoRelation', 'departamentoEncargadoRelation'])
                ->orderBy('s_id', 'desc')
                ->get();
        } else {
            $solicitudes = Solicitud::where('solicitante', $user->id)
                ->with(['tipoRelation', 'encargadoRelation', 'solicitantextRelation', 'estadoRelation', 'departamentoEncargadoRelation'])
                ->orderBy('s_id', 'desc')
                ->get();
        }

        return response()->json($solicitudes);
    }

    /**
     * Display requests assigned to the authenticated user (as agent/encargado).
     */
    public function asignadas()
    {
        $user = auth('api')->user();

        $solicitudes = Solicitud::where('encargado', $user->id)
            ->with(['tipoRelation', 'solicitanteRelation', 'solicitantextRelation', 'estadoRelation', 'departamentoEncargadoRelation'])
            ->orderBy('s_id', 'desc')
            ->get();

        return response()->json($solicitudes);
    }

    /**
     * Store a newly created request and auto-assign the agent (replacing procedure logic).
     */
    public function store(Request $request)
    {
        $tipoUuid = $request->tipo;
        $solicitudTipo = \App\Models\SolicitudTipo::with('steps')->where('uuid', $tipoUuid)->first();

        if (!$solicitudTipo) {
            return response()->json(['tipo' => ['El tipo de solicitud especificado no existe o no está configurado.']], 400);
        }

        // Build dynamic validation rules
        $rules = [
            'tipo' => 'required|string|exists:solicitud_tipo,uuid',
            'solicitantext' => 'nullable|integer',
        ];

        if ($solicitudTipo->requiere_documento) {
            $rules['s_doc'] = 'required|string|max:255';
        } else {
            $rules['s_doc'] = 'nullable|string|max:255';
        }

        if ($solicitudTipo->requiere_valor) {
            $rules['s_valor'] = 'required|numeric';
        } else {
            $rules['s_valor'] = 'nullable|numeric';
        }

        if ($solicitudTipo->requiere_descripcion) {
            $rules['descripcion'] = 'required|string|max:255';
        } else {
            $rules['descripcion'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Find the first step of the workflow
        $firstStep = $solicitudTipo->steps()->orderBy('orden', 'asc')->first();

        $departamentoId = null;
        $encargadoId = null;
        $currentStepId = null;

        if ($firstStep) {
            $departamentoId = $firstStep->rol_id;
            $currentStepId = $firstStep->id;

            // Find the agent with the specified role having the lowest amount of active requests (estado in [1, 2, 3])
            $encargado = Usuario::whereHas('roles', function ($query) use ($departamentoId) {
                    $query->where('rol.id', $departamentoId);
                })
                ->leftJoin('solicitud', function ($join) {
                    $join->on('usuario.id', '=', 'solicitud.encargado')
                         ->whereIn('solicitud.estado', [1, 2, 3]);
                })
                ->select('usuario.id', DB::raw('count(solicitud.s_id) as active_count'))
                ->groupBy('usuario.id')
                ->orderBy('active_count', 'asc')
                ->orderBy('usuario.id', 'asc')
                ->first();

            if ($encargado) {
                $encargadoId = $encargado->id;
            }
        }

        $solicitud = Solicitud::create([
            's_fecha' => now(),
            's_doc' => $request->s_doc,
            's_valor' => $request->s_valor,
            'tipo' => $solicitudTipo->id_tipo,
            'solicitante' => auth('api')->id(),
            'encargado' => $encargadoId,
            'solicitantext' => $request->solicitantext,
            'descripcion' => $request->descripcion,
            'estado' => 1, // State 1 = Pendiente/En trámite
            'departamento_encargado' => $departamentoId,
            'current_step_id' => $currentStepId,
        ]);

        return response()->json([
            'message' => 'Solicitud creada y asignada exitosamente',
            'solicitud' => $solicitud->load(['tipoRelation', 'encargadoRelation', 'estadoRelation'])
        ], 201);
    }

    /**
     * Display the specified request.
     */
    public function show($uuid)
    {
        $solicitud = Solicitud::with([
            'tipoRelation', 
            'solicitanteRelation', 
            'encargadoRelation', 
            'solicitantextRelation', 
            'estadoRelation', 
            'departamentoEncargadoRelation',
            'historiales.responsableUsuario',
            'historiales.departamentoRol',
            'historiales.estadoRelation'
        ])->where('uuid', $uuid)->first();

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        return response()->json($solicitud);
    }

    /**
     * Update the specified request.
     */
    public function update(Request $request, $uuid)
    {
        $solicitud = Solicitud::where('uuid', $uuid)->first();

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            's_doc' => 'nullable|string|max:255',
            's_valor' => 'nullable|numeric',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|integer', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $solicitud->update($request->only(['s_doc', 's_valor', 'descripcion', 'estado']));

        return response()->json([
            'message' => 'Solicitud actualizada exitosamente',
            'solicitud' => $solicitud->load(['tipoRelation', 'encargadoRelation', 'estadoRelation'])
        ]);
    }

    /**
     * Delete a request.
     */
    public function destroy($uuid)
    {
        $solicitud = Solicitud::where('uuid', $uuid)->first();

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $solicitud->delete();

        return response()->json(['message' => 'Solicitud eliminada exitosamente']);
    }

    /**
     * Reassign the request manually to a different agent or department.
     */
    public function reassign(Request $request, $uuid)
    {
        $solicitud = Solicitud::where('uuid', $uuid)->first();

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'encargado' => 'nullable|string|exists:usuario,uuid',
            'departamento_encargado' => 'nullable|string|exists:rol,uuid',
            'tipo' => 'nullable|string|exists:solicitud_tipo,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('encargado') && !empty($request->encargado)) {
            $encUser = Usuario::where('uuid', $request->encargado)->first();
            if ($encUser) {
                $solicitud->encargado = $encUser->id;
            }
        }

        if ($request->has('departamento_encargado') && !empty($request->departamento_encargado)) {
            $encDept = \App\Models\Rol::where('uuid', $request->departamento_encargado)->first();
            if ($encDept) {
                $solicitud->departamento_encargado = $encDept->id;
            }
        }

        if ($request->has('tipo') && !empty($request->tipo)) {
            $solType = \App\Models\SolicitudTipo::where('uuid', $request->tipo)->first();
            if ($solType) {
                $solicitud->tipo = $solType->id_tipo;
            }
        }

        $solicitud->save();

        return response()->json([
            'message' => 'Solicitud reasignada exitosamente',
            'solicitud' => $solicitud->load(['tipoRelation', 'encargadoRelation', 'estadoRelation', 'departamentoEncargadoRelation'])
        ]);
    }
}
