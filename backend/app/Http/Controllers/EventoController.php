<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventoController extends Controller
{
    public function index()
    {
        // Replaces ObtenerInfoEventos procedure by loading the relation
        return response()->json(Evento::with('deporte')->orderBy('fecha_inicio', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|string|max:1000',
            'descripcion' => 'nullable|string|max:300',
            'deporte_id' => 'required|integer|exists:deportes,id',
            'inscripciones' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Note: The 'estado' and 'fecha_eliminar' are automatically set in the Evento model's booted saving event!
        $evento = Evento::create($request->all());

        return response()->json($evento, 201);
    }

    public function show($id)
    {
        $evento = Evento::with(['deporte', 'inscripcionesEventos'])->find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return response()->json($evento);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:100',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|string|max:1000',
            'descripcion' => 'nullable|string|max:300',
            'deporte_id' => 'sometimes|required|integer|exists:deportes,id',
            'inscripciones' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $evento->update($request->all());

        return response()->json($evento);
    }

    public function destroy($id)
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $evento->delete();

        return response()->json(['message' => 'Evento eliminado']);
    }
}
