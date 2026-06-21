<?php

namespace App\Http\Controllers;

use App\Models\Rol;

class RoleController extends Controller
{
    public function index()
    {
        // List roles except Deportista (5), Entrenador (6), Publicista (7) and Administrador (8)
        // since those do not intervene in the request approval workflow steps.
        return response()->json(
            Rol::where('deleted', false)
                ->whereNotIn('id', [5, 6, 7, 8])
                ->get()
        );
    }
}
