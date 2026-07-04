<?php

namespace App\Http\Controllers;

use App\Models\CartaCondolencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CartaCondolenciaController extends Controller
{
    public function index()
    {
        return response()->json(CartaCondolencia::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mensaje' => 'required|string|max:700',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'fecha_eliminar' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('cartas', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $carta = CartaCondolencia::create($data);

        return response()->json($carta, 201);
    }

    public function show($uuid)
    {
        $carta = CartaCondolencia::where('uuid', $uuid)->first();

        if (!$carta) {
            return response()->json(['message' => 'Carta no encontrada'], 404);
        }

        return response()->json($carta);
    }

    public function update(Request $request, $uuid)
    {
        $carta = CartaCondolencia::where('uuid', $uuid)->first();

        if (!$carta) {
            return response()->json(['message' => 'Carta no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'mensaje' => 'sometimes|required|string|max:700',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'fecha_eliminar' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($carta->imagen) {
                $oldPath = str_replace('/storage/', '', $carta->imagen);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('cartas', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $carta->update($data);

        return response()->json($carta);
    }

    public function destroy($uuid)
    {
        $carta = CartaCondolencia::where('uuid', $uuid)->first();

        if (!$carta) {
            return response()->json(['message' => 'Carta no encontrada'], 404);
        }

        if ($carta->imagen) {
            $oldPath = str_replace('/storage/', '', $carta->imagen);
            Storage::disk('public')->delete($oldPath);
        }

        $carta->delete();

        return response()->json(['message' => 'Carta eliminada']);
    }
}
