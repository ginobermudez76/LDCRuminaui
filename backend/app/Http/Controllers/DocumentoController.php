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
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:2000',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max
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

    public function show($id)
    {
        $documento = Documento::find($id);

        if (!$documento) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        return response()->json($documento);
    }

    public function update(Request $request, $id)
    {
        $documento = Documento::find($id);

        if (!$documento) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:200',
            'descripcion' => 'nullable|string|max:2000',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
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

    public function destroy($id)
    {
        $documento = Documento::find($id);

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
