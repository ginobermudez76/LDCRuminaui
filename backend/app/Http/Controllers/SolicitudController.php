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

        // Admin (has REGISTRAR_USUARIOS option) or Presidente (rol 1) sees all
        if ($user->rol == 1 || $user->hasOption('REGISTRAR_USUARIOS')) {
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
        $tipoId = $request->tipo;
        $solicitudTipo = \App\Models\SolicitudTipo::with('steps')->find($tipoId);

        if (!$solicitudTipo) {
            return response()->json(['tipo' => ['El tipo de solicitud especificado no existe o no está configurado.']], 400);
        }

        // Build dynamic validation rules
        $rules = [
            'tipo' => 'required|integer|exists:solicitud_tipo,id_tipo',
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
            'tipo' => $tipoId,
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
    public function show($id)
    {
        $solicitud = Solicitud::with(['tipoRelation', 'solicitanteRelation', 'encargadoRelation', 'solicitantextRelation', 'estadoRelation', 'departamentoEncargadoRelation'])
            ->find($id);

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        return response()->json($solicitud);
    }

    /**
     * Update the specified request (edit details or change status like Approve/Deny).
     */
    public function update(Request $request, $id)
    {
        $solicitud = Solicitud::find($id);

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            's_doc' => 'nullable|string|max:255',
            's_valor' => 'nullable|numeric',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|integer', // Can be updated to 2 (Aprobada), 3 (Denegada), etc.
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
    public function destroy($id)
    {
        $solicitud = Solicitud::find($id);

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $solicitud->delete();

        return response()->json(['message' => 'Solicitud eliminada exitosamente']);
    }

    /**
     * Reassign the request manually to a different agent or department (replacing reassign scripts).
     */
    public function reassign(Request $request, $id)
    {
        $solicitud = Solicitud::find($id);

        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'encargado' => 'nullable|integer|exists:usuario,id',
            'departamento_encargado' => 'nullable|integer|exists:rol,id',
            'tipo' => 'nullable|integer|exists:solicitud_tipo,id_tipo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('encargado')) {
            $solicitud->encargado = $request->encargado;
        }

        if ($request->has('departamento_encargado')) {
            $solicitud->departamento_encargado = $request->departamento_encargado;
        }

        if ($request->has('tipo')) {
            $solicitud->tipo = $request->tipo;
        }

        $solicitud->save();

        return response()->json([
            'message' => 'Solicitud reasignada exitosamente',
            'solicitud' => $solicitud->load(['tipoRelation', 'encargadoRelation', 'estadoRelation', 'departamentoEncargadoRelation'])
        ]);
    }
}
