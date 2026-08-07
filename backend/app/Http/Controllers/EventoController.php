<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventoController extends Controller
{
    public function index()
    {
        return response()->json(Evento::with('deporte')->orderBy('fecha_inicio', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'descripcion' => 'nullable|string|max:300',
            'deporte_id' => 'required|string|exists:deportes,uuid',
            'inscripciones' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except(['imagen', 'deporte_id']);

        $deporte = Deporte::where('uuid', $request->deporte_id)->firstOrFail();
        $data['deporte_id'] = $deporte->id;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('eventos', 'public');
            $data['imagen'] = '/storage/'.$path;
        }

        $evento = Evento::create($data);

        return response()->json($evento, 201);
    }

    public function show($uuid)
    {
        $evento = Evento::with(['deporte', 'inscripcionesEventos'])->where('uuid', $uuid)->first();

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return response()->json($evento);
    }

    public function update(Request $request, $uuid)
    {
        $evento = Evento::where('uuid', $uuid)->first();

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:100',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'descripcion' => 'nullable|string|max:300',
            'deporte_id' => 'sometimes|required|string|exists:deportes,uuid',
            'inscripciones' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except(['imagen', 'deporte_id']);

        if ($request->has('deporte_id')) {
            $deporte = Deporte::where('uuid', $request->deporte_id)->firstOrFail();
            $data['deporte_id'] = $deporte->id;
        }

        if ($request->hasFile('imagen')) {
            if ($evento->imagen) {
                $oldPath = str_replace('/storage/', '', $evento->imagen);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('eventos', 'public');
            $data['imagen'] = '/storage/'.$path;
        }

        $evento->update($data);

        return response()->json($evento);
    }

    public function destroy($uuid)
    {
        $evento = Evento::where('uuid', $uuid)->first();

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        if ($evento->imagen) {
            $oldPath = str_replace('/storage/', '', $evento->imagen);
            Storage::disk('public')->delete($oldPath);
        }

        $evento->delete();

        return response()->json(['message' => 'Evento eliminado']);
    }
}
