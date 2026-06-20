<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticiaController extends Controller
{
    public function index()
    {
        return response()->json(Noticia::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:100',
            'imagen' => 'nullable|string|max:1000',
            'cuerpo' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $noticia = Noticia::create($request->all());

        return response()->json($noticia, 201);
    }

    public function show($id)
    {
        $noticia = Noticia::find($id);

        if (!$noticia) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        return response()->json($noticia);
    }

    public function update(Request $request, $id)
    {
        $noticia = Noticia::find($id);

        if (!$noticia) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        $noticia->update($request->all());

        return response()->json($noticia);
    }

    public function destroy($id)
    {
        $noticia = Noticia::find($id);

        if (!$noticia) {
            return response()->json(['message' => 'Noticia no encontrada'], 404);
        }

        $noticia->delete();

        return response()->json(['message' => 'Noticia eliminada']);
    }
}
