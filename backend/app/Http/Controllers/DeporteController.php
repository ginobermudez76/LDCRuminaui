<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeporteController extends Controller
{
    public function index()
    {
        return response()->json(Deporte::with('deportistasDestacados')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:300',
            'imagen' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $deporte = Deporte::create($request->all());

        return response()->json($deporte, 201);
    }

    public function show($id)
    {
        $deporte = Deporte::with(['deportistasDestacados', 'eventos', 'logros'])->find($id);

        if (!$deporte) {
            return response()->json(['message' => 'Deporte no encontrado'], 404);
        }

        return response()->json($deporte);
    }

    public function update(Request $request, $id)
    {
        $deporte = Deporte::find($id);

        if (!$deporte) {
            return response()->json(['message' => 'Deporte no encontrado'], 404);
        }

        $deporte->update($request->all());

        return response()->json($deporte);
    }

    public function destroy($id)
    {
        $deporte = Deporte::find($id);

        if (!$deporte) {
            return response()->json(['message' => 'Deporte no encontrado'], 404);
        }

        $deporte->delete();

        return response()->json(['message' => 'Deporte eliminado']);
    }
}
