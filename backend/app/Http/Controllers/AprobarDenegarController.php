<?php

namespace App\Http\Controllers;

use App\Models\HistorialSolicitud;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AprobarDenegarController extends Controller
{
    public function procesar(Request $request, $uuid)
    {
        $request->validate([
            'accion' => 'required|in:Aprobar,Denegar',
        ]);

        $solicitud = Solicitud::where('uuid', $uuid)->firstOrFail();
        $usuario = JWTAuth::user();

        $rol = $usuario->roles->first();
        if (! $rol) {
            return response()->json(['error' => 'Usuario no tiene rol asignado'], 403);
        }

        $rolId = $rol->id;
        $accion = $request->accion;
        $tipoId = $solicitud->tipo;

        $isAdmin = $rol->codigo === 'ADMINISTRADOR';

        // Verify if user is current encargado OR if they are Admin
        if ($solicitud->encargado !== $usuario->id && ! $isAdmin) {
            return response()->json(['error' => 'No está asignado como encargado de esta solicitud'], 403);
        }

        if ($accion === 'Denegar') {
            return $this->denegar($solicitud, $usuario, $rolId, $tipoId);
        }

        // Admin (role with code ADMINISTRADOR) approves directly to state 5 (Fully Approved) only if they are not the assigned encargado
        if ($isAdmin && $solicitud->encargado !== $usuario->id) {
            return $this->aprobarAdministrador($solicitud, $usuario, $rolId, $tipoId);
        }

        $currentStep = WorkflowStep::find($solicitud->current_step_id);

        return $this->avanzarSiguientePaso($solicitud, $usuario, $rolId, $tipoId, $currentStep);
    }

    private function denegar(Solicitud $solicitud, $usuario, $rolId, $tipoId)
    {
        $nuevo_estado = 4; // State 4 = Rechazada

        DB::beginTransaction();
        try {
            $solicitud->estado = $nuevo_estado;
            $solicitud->encargado = null;
            $solicitud->departamento_encargado = null;
            $solicitud->current_step_id = null;
            $solicitud->save();

            HistorialSolicitud::create([
                'solicitud_id' => $solicitud->s_id,
                'estado' => $nuevo_estado,
                'responsable' => $usuario->id,
                'departamento' => $rolId,
                'tipo' => $tipoId,
            ]);

            DB::commit();

            return response()->json(['message' => 'Solicitud denegada', 'estado' => $nuevo_estado]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function aprobarAdministrador(Solicitud $solicitud, $usuario, $rolId, $tipoId)
    {
        DB::beginTransaction();
        try {
            $solicitud->estado = 5; // Aprobada
            $solicitud->departamento_encargado = null;
            $solicitud->encargado = null;
            $solicitud->current_step_id = null;
            $solicitud->save();

            HistorialSolicitud::create([
                'solicitud_id' => $solicitud->s_id,
                'estado' => 5,
                'responsable' => $usuario->id,
                'departamento' => $rolId,
                'tipo' => $tipoId,
            ]);

            DB::commit();

            return response()->json(['message' => 'Solicitud aprobada por Administrador', 'estado' => 5]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function avanzarSiguientePaso(Solicitud $solicitud, $usuario, $rolId, $tipoId, $currentStep)
    {
        // Find the next step in order
        $nextStep = WorkflowStep::where('solicitud_tipo_id', $tipoId)
            ->where('orden', '>', $currentStep ? $currentStep->orden : 0)
            ->orderBy('orden', 'asc')
            ->first();

        DB::beginTransaction();
        try {
            if ($nextStep) {
                // Determine state based on order: step 1 = 1, step 2 = 2, step 3+ = 3
                $nuevo_estado = $this->determinarNuevoEstado($nextStep->orden);
                $nextDepartamentoId = $nextStep->rol_id;
                $nextEncargadoId = $this->buscarEncargadoCargaMinima($nextDepartamentoId);

                $solicitud->estado = $nuevo_estado;
                $solicitud->departamento_encargado = $nextDepartamentoId;
                $solicitud->encargado = $nextEncargadoId;
                $solicitud->current_step_id = $nextStep->id;
                $solicitud->save();

            } else {
                // No more steps: Fully Approved
                $nuevo_estado = 5; // Aprobada
                $solicitud->estado = $nuevo_estado;
                $solicitud->departamento_encargado = null;
                $solicitud->encargado = null;
                $solicitud->current_step_id = null;
                $solicitud->save();
            }

            // Create history record
            HistorialSolicitud::create([
                'solicitud_id' => $solicitud->s_id,
                'estado' => $nuevo_estado,
                'responsable' => $usuario->id,
                'departamento' => $rolId,
                'tipo' => $tipoId,
            ]);

            DB::commit();

            return response()->json(['message' => 'Solicitud procesada con éxito', 'estado' => $nuevo_estado]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function determinarNuevoEstado(int $orden): int
    {
        if ($orden === 1) {
            return 1;
        }
        if ($orden === 2) {
            return 2;
        }
        return 3;
    }

    private function buscarEncargadoCargaMinima(int $nextDepartamentoId)
    {
        // Find user with lowest workload for the next role
        $encargado = Usuario::whereHas('roles', function ($query) use ($nextDepartamentoId) {
            $query->where('rol.id', $nextDepartamentoId);
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

        return $encargado ? $encargado->id : null;
    }
}
