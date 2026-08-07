<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use App\Models\DeportistaDestacado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeportistaDestacadoController extends Controller
{
    public function index()
    {
        return response()->json(DeportistaDestacado::with('deporte')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_deportista' => 'required|string|max:200',
            'deporte_id' => 'nullable|string|exists:deportes,uuid',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except(['imagen', 'deporte_id']);

        if ($request->filled('deporte_id')) {
            $deporte = Deporte::where('uuid', $request->deporte_id)->first();
            if ($deporte) {
                $data['deporte_id'] = $deporte->id;
            }
        }

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('deportistas', 'public');
            $data['imagen'] = '/storage/'.$path;
        }

        $deportista = DeportistaDestacado::create($data);

        return response()->json($deportista, 201);
    }

    public function show($uuid)
    {
        $deportista = DeportistaDestacado::with('deporte')->where('uuid', $uuid)->first();

        if (! $deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        return response()->json($deportista);
    }

    public function update(Request $request, $uuid)
    {
        $deportista = DeportistaDestacado::where('uuid', $uuid)->first();

        if (! $deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_deportista' => 'sometimes|required|string|max:200',
            'deporte_id' => 'nullable|string|exists:deportes,uuid',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except(['imagen', 'deporte_id']);

        if ($request->has('deporte_id')) {
            if ($request->filled('deporte_id')) {
                $deporte = Deporte::where('uuid', $request->deporte_id)->first();
                $data['deporte_id'] = $deporte ? $deporte->id : null;
            } else {
                $data['deporte_id'] = null;
            }
        }

        if ($request->hasFile('imagen')) {
            if ($deportista->imagen) {
                $oldPath = str_replace('/storage/', '', $deportista->imagen);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('deportistas', 'public');
            $data['imagen'] = '/storage/'.$path;
        }

        $deportista->update($data);

        return response()->json($deportista);
    }

    public function destroy($uuid)
    {
        $deportista = DeportistaDestacado::where('uuid', $uuid)->first();

        if (! $deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        if ($deportista->imagen) {
            $oldPath = str_replace('/storage/', '', $deportista->imagen);
            Storage::disk('public')->delete($oldPath);
        }

        $deportista->delete();

        return response()->json(['message' => 'Deportista eliminado']);
    }
}
