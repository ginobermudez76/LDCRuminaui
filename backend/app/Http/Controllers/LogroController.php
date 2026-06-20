<?php

namespace App\Http\Controllers;

use App\Models\Logro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogroController extends Controller
{
    public function index()
    {
        // Replaces info_logros procedure by loading relations
        return response()->json(Logro::with('deporte')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'deporte_id' => 'required|integer|exists:deportes,id',
            'imagen' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $logro = Logro::create($request->all());

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

        $logro->update($request->all());

        return response()->json($logro);
    }

    public function destroy($id)
    {
        $logro = Logro::find($id);

        if (!$logro) {
            return response()->json(['message' => 'Logro no encontrado'], 404);
        }

        $logro->delete();

        return response()->json(['message' => 'Logro eliminado']);
    }
}
