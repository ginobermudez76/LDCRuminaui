<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        return response()->json(Documento::all());
    }

    public function store(Request $request)
    {
        if ($request->header('Content-Length') && (int)$request->header('Content-Length') > 8388608) {
            return response()->json(['message' => 'El tamaño del contenido excede el límite permitido de 8MB'], 413);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:2000',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:8192', // 8MB max
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('documento');

        if ($request->hasFile('documento')) {
            $path = $request->file('documento')->store('documentos', 'public');
            $data['documento'] = '/storage/' . $path;
        }

        $documento = Documento::create($data);

        return response()->json($documento, 201);
    }

    public function show($uuid)
    {
        $documento = Documento::where('uuid', $uuid)->first();

        if (!$documento) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        return response()->json($documento);
    }

    public function update(Request $request, $uuid)
    {
        $documento = Documento::where('uuid', $uuid)->first();

        if (!$documento) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        if ($request->header('Content-Length') && (int)$request->header('Content-Length') > 8388608) {
            return response()->json(['message' => 'El tamaño del contenido excede el límite permitido de 8MB'], 413);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:200',
            'descripcion' => 'nullable|string|max:2000',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:8192',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('documento');

        if ($request->hasFile('documento')) {
            if ($documento->documento) {
                $oldPath = str_replace('/storage/', '', $documento->documento);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('documento')->store('documentos', 'public');
            $data['documento'] = '/storage/' . $path;
        }

        $documento->update($data);

        return response()->json($documento);
    }

    public function destroy($uuid)
    {
        $documento = Documento::where('uuid', $uuid)->first();

        if (!$documento) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        if ($documento->documento) {
            $oldPath = str_replace('/storage/', '', $documento->documento);
            Storage::disk('public')->delete($oldPath);
        }

        $documento->delete();

        return response()->json(['message' => 'Documento eliminado']);
    }
}
