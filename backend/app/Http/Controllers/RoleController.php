<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Rol::where('deleted', false);

        if ($request->has('all')) {
            return response()->json($query->get());
        }

        // List roles except Deportista (5), Entrenador (6), Publicista (7) and Administrador (8)
        // since those do not intervene in the request approval workflow steps.
        return response()->json(
            $query->whereNotIn('id', [5, 6, 7, 8])->get()
        );
    }
}
