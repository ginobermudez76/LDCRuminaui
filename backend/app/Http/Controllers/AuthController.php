<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'primer_nombre' => 'required|string|max:45',
            'segundo_nombre' => 'nullable|string|max:45',
            'primer_apellido' => 'required|string|max:45',
            'segundo_apellido' => 'nullable|string|max:45',
            'cedula' => 'required|string|max:10|unique:usuario,cedula',
            'celular' => 'nullable|string|max:10',
            'correo' => 'required|string|email|max:100', // Unique check removed from email to support legacy duplicate example@gmail.com
            'nombre_usuario' => 'required|string|max:150|unique:usuario,nombre_usuario',
            'contrasena' => 'required|string|min:6',
            'rol' => 'nullable|integer',
            'fecha_nac' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Map frontend inputs to new DB structure
        $nombres = trim(($request->primer_nombre ?? '') . ' ' . ($request->segundo_nombre ?? ''));
        $apellidos = trim(($request->primer_apellido ?? '') . ' ' . ($request->segundo_apellido ?? ''));

        $usuario = Usuario::create([
            'uuid' => (string) Str::uuid(),
            'nombre_usuario' => $request->nombre_usuario,
            'correo_electronico' => $request->correo,
            'password_hash' => Hash::make($request->contrasena),
            'nombres' => $nombres !== '' ? $nombres : null,
            'apellidos' => $apellidos !== '' ? $apellidos : null,
            'cedula' => $request->cedula,
            'celular' => $request->celular,
            'fecha_nac' => $request->fecha_nac,
            'activo' => true,
            'deleted' => false,
        ]);

        // Attach role to the many-to-many relationship
        $rolId = $request->rol ?? 8; // Default to Administrador or custom role
        $usuario->roles()->attach($rolId, ['uuid' => (string) Str::uuid()]);

        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $usuario->load('roles'),
            'token' => $token,
        ], 201);
    }

    /**
     * Authenticate user and return token.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('nombre_usuario', 'contrasena');

        $validator = Validator::make($credentials, [
            'nombre_usuario' => 'required|string',
            'contrasena' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $authCredentials = [
            'nombre_usuario' => $credentials['nombre_usuario'],
            'password' => $credentials['contrasena']
        ];

        try {
            if (!$token = auth('api')->attempt($authCredentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Error generating token: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'No se pudo crear el token: ' . $e->getMessage()], 500);
        }

        $user = auth('api')->user();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile()
    {
        return response()->json(auth('api')->user()->load('roles'));
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}
