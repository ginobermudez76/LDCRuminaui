<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all active users with their roles loaded (rol_relation is an appended attribute)
        $usuarios = Usuario::with('roles')->where('deleted', 0)->get();
        return response()->json($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:45',
            'snombre' => 'nullable|string|max:45',
            'apellido' => 'required|string|max:45',
            'sapellido' => 'nullable|string|max:45',
            'cedula' => 'required|string|max:10',
            'celular' => 'nullable|string|max:10',
            'correo_electronico' => 'required|email|max:100',
            'fecha_nac' => 'required|date',
            'rol_id' => 'required|integer|exists:rol,id',
            'username' => 'required|string|unique:usuario,nombre_usuario',
            'password' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Construct full names
            $nombres = trim($validatedData['nombre'] . ' ' . ($validatedData['snombre'] ?? ''));
            $apellidos = trim($validatedData['apellido'] . ' ' . ($validatedData['sapellido'] ?? ''));

            // Create user
            $usuario = new Usuario();
            $usuario->uuid = (string) Str::uuid();
            $usuario->nombre_usuario = $validatedData['username'];
            $usuario->correo_electronico = $validatedData['correo_electronico'];
            $usuario->password_hash = Hash::make($validatedData['password']);
            $usuario->nombres = $nombres;
            $usuario->apellidos = $apellidos;
            $usuario->cedula = $validatedData['cedula'];
            $usuario->celular = $validatedData['celular'];
            $usuario->fecha_nac = $validatedData['fecha_nac'];
            $usuario->activo = 1;
            $usuario->deleted = 0;
            $usuario->save();

            // Assign role
            DB::table('rol_usuario')->insert([
                'uuid' => (string) Str::uuid(),
                'id_rol' => $validatedData['rol_id'],
                'id_usuario' => $usuario->id,
                'deleted' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Load role to return
            $usuario->load('roles');

            return response()->json($usuario, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear el usuario: ' . $e->getMessage()], 500);
        }
    }
}
