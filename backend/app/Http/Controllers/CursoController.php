<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CursoController extends Controller
{
    public function index()
    {
        return response()->json(Curso::with('deporte')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:300',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'deporte_id' => 'nullable|exists:deportes,id',
            'estado' => 'nullable|string|max:50',
            'inscripciones' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('cursos', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $curso = Curso::create($data);

        return response()->json($curso, 201);
    }

    public function show($id)
    {
        $curso = Curso::with('deporte')->find($id);

        if (!$curso) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }

        return response()->json($curso);
    }

    public function update(Request $request, $id)
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:100',
            'descripcion' => 'nullable|string|max:300',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'deporte_id' => 'nullable|exists:deportes,id',
            'estado' => 'nullable|string|max:50',
            'inscripciones' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($curso->imagen) {
                $oldPath = str_replace('/storage/', '', $curso->imagen);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('cursos', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $curso->update($data);

        return response()->json($curso);
    }

    public function destroy($id)
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }

        if ($curso->imagen) {
            $oldPath = str_replace('/storage/', '', $curso->imagen);
            Storage::disk('public')->delete($oldPath);
        }

        $curso->delete();

        return response()->json(['message' => 'Curso eliminado']);
    }
}
