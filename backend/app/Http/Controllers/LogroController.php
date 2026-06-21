<?php

namespace App\Http\Controllers;

use App\Models\Logro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LogroController extends Controller
{
    public function index()
    {
        return response()->json(Logro::with('deporte')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'deporte_id' => 'nullable|integer|exists:deportes,id',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('logros', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $logro = Logro::create($data);

        return response()->json($logro, 201);
    }

    public function show($id)
    {
        $logro = Logro::with('deporte')->find($id);

        if (!$logro) {
            return response()->json(['message' => 'Logro no encontrado'], 404);
        }

        return response()->json($logro);
    }

    public function update(Request $request, $id)
    {
        $logro = Logro::find($id);

        if (!$logro) {
            return response()->json(['message' => 'Logro no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:200',
            'deporte_id' => 'nullable|integer|exists:deportes,id',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            if ($logro->imagen) {
                $oldPath = str_replace('/storage/', '', $logro->imagen);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('logros', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $logro->update($data);

        return response()->json($logro);
    }

    public function destroy($id)
    {
        $logro = Logro::find($id);

        if (!$logro) {
            return response()->json(['message' => 'Logro no encontrado'], 404);
        }

        if ($logro->imagen) {
            $oldPath = str_replace('/storage/', '', $logro->imagen);
            Storage::disk('public')->delete($oldPath);
        }

        $logro->delete();

        return response()->json(['message' => 'Logro eliminado']);
    }
}
