<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeporteController extends Controller
{
    private const STORAGE_PREFIX = '/storage/';

    private const NOT_FOUND_MSG = 'Deporte no encontrado';

    public function index()
    {
        return response()->json(Deporte::with('deportistasDestacados')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:300',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('deportes', 'public');
            $data['imagen'] = self::STORAGE_PREFIX.$path;
        }

        $deporte = Deporte::create($data);

        return response()->json($deporte, 201);
    }

    public function show($uuid)
    {
        $deporte = Deporte::with(['deportistasDestacados', 'eventos', 'logros'])->where('uuid', $uuid)->first();

        if (! $deporte) {
            return response()->json(['message' => self::NOT_FOUND_MSG], 404);
        }

        return response()->json($deporte);
    }

    public function update(Request $request, $uuid)
    {
        $deporte = Deporte::where('uuid', $uuid)->first();

        if (! $deporte) {
            return response()->json(['message' => self::NOT_FOUND_MSG], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:100',
            'descripcion' => 'nullable|string|max:300',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            // Delete old image if exists
            if ($deporte->imagen) {
                $oldPath = str_replace(self::STORAGE_PREFIX, '', $deporte->imagen);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('deportes', 'public');
            $data['imagen'] = self::STORAGE_PREFIX.$path;
        }

        $deporte->update($data);

        return response()->json($deporte);
    }

    public function destroy($uuid)
    {
        $deporte = Deporte::where('uuid', $uuid)->first();

        if (! $deporte) {
            return response()->json(['message' => self::NOT_FOUND_MSG], 404);
        }

        if ($deporte->imagen) {
            $oldPath = str_replace(self::STORAGE_PREFIX, '', $deporte->imagen);
            Storage::disk('public')->delete($oldPath);
        }

        $deporte->delete();

        return response()->json(['message' => 'Deporte eliminado']);
    }
}
