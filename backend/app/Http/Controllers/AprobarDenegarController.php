<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\HistorialSolicitud;
use App\Models\SolicitudTipo;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AprobarDenegarController extends Controller
{
    public function procesar(Request $request, $id)
    {
        $request->validate([
            'accion' => 'required|in:Aprobar,Denegar',
        ]);

        $solicitud = Solicitud::with('tipoRelation')->findOrFail($id);
        $usuario = JWTAuth::user();
        
        $rol = $usuario->roles->first();
        if (!$rol) {
            return response()->json(['error' => 'Usuario no tiene rol asignado'], 403);
        }

        $rolId = $rol->id;
        $accion = $request->accion;
        $tipoNombre = $solicitud->tipoRelation ? $solicitud->tipoRelation->name_tipo : '';
        $tipoId = $solicitud->tipo;

        $nuevo_estado = null;

        if ($accion !== 'Denegar') {
            if ($tipoNombre !== 'Otro tipo') {
                if ($rolId != 8) { // 8 is Administrador
                    switch ($rolId) {
                        case 9: // Secretaría
                            $nuevo_estado = 2;
                            break;
                        case 2: // Metodólogo
                        case 4: // Coordinador general
                            $nuevo_estado = 3;
                            break;
                        case 3: // Tesorería
                            $nuevo_estado = 5;
                            break;
                    }

                    if ($nuevo_estado !== null) {
                        try {
                            DB::beginTransaction();
                            $solicitud->estado = $nuevo_estado;
                            $solicitud->save();

                            // Call stored procedure
                            DB::statement("CALL procesarAccionSP(?, ?, ?)", [$nuevo_estado, $tipoId, $id]);

                            // Historial implementation (applied to all as planned)
                            HistorialSolicitud::create([
                                'solicitud_id' => $id,
                                'estado' => $nuevo_estado,
                                'responsable' => $usuario->id,
                                'departamento' => $rolId,
                                'tipo' => $tipoId,
                            ]);

                            DB::commit();
                            return response()->json(['message' => 'Solicitud procesada', 'estado' => $nuevo_estado]);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return response()->json(['error' => $e->getMessage()], 500);
                        }
                    } else {
                        return response()->json(['error' => 'El rol no tiene permisos para aprobar esta solicitud'], 403);
                    }
                } else {
                    // Admin
                    $nuevo_estado = 5;
                    $this->updateEstadoAndHistorial($solicitud, $nuevo_estado, $usuario->id, $rolId, $tipoId);
                    return response()->json(['message' => 'Solicitud aprobada por Administrador', 'estado' => $nuevo_estado]);
                }
            } else {
                // Otro tipo
                $nuevo_estado = 5;
                $this->updateEstadoAndHistorial($solicitud, $nuevo_estado, $usuario->id, $rolId, $tipoId);
                return response()->json(['message' => 'Solicitud de Otro tipo aprobada', 'estado' => $nuevo_estado]);
            }
        } else {
            // Denegar
            $nuevo_estado = 4;
            $this->updateEstadoAndHistorial($solicitud, $nuevo_estado, $usuario->id, $rolId, $tipoId);
            return response()->json(['message' => 'Solicitud denegada', 'estado' => $nuevo_estado]);
        }
    }

    private function updateEstadoAndHistorial($solicitud, $estado, $usuarioId, $rolId, $tipoId)
    {
        DB::beginTransaction();
        try {
            $solicitud->estado = $estado;
            $solicitud->encargado = $usuarioId;
            $solicitud->departamento_encargado = $rolId;
            $solicitud->save();

            HistorialSolicitud::create([
                'solicitud_id' => $solicitud->s_id,
                'estado' => $estado,
                'responsable' => $usuarioId,
                'departamento' => $rolId,
                'tipo' => $tipoId,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
