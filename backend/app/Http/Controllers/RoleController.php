<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $query = Rol::where('deleted', false);

        // Return all roles so any role can be added to the request workflow
        return response()->json($query->get());
    }
}
