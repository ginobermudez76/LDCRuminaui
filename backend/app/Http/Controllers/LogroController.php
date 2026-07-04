<?php

namespace App\Http\Controllers;

use App\Models\Logro;
use App\Models\Deporte;
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
            'deporte_id' => 'nullable|string|exists:deportes,uuid',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'tipologro' => 'nullable|string|in:Medalla,Copa,Reconocimiento',
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
            $path = $request->file('imagen')->store('logros', 'public');
            $data['imagen'] = '/storage/' . $path;
        }

        $logro = Logro::create($data);

        return response()->json($logro, 201);
    }

    public function show($uuid)
    {
        $logro = Logro::with('deporte')->where('uuid', $uuid)->first();

        if (!$logro) {
            return response()->json(['message' => 'Logro no encontrado'], 404);
        }

        return response()->json($logro);
    }

    public function update(Request $request, $uuid)
    {
        $logro = Logro::where('uuid', $uuid)->first();

        if (!$logro) {
            return response()->json(['message' => 'Logro no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:200',
            'deporte_id' => 'nullable|string|exists:deportes,uuid',
            'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'tipologro' => 'nullable|string|in:Medalla,Copa,Reconocimiento',
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

    public function destroy($uuid)
    {
        $logro = Logro::where('uuid', $uuid)->first();

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
