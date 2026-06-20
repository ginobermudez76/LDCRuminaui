<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            'cedula' => 'required|string|max:10|unique:usuarios,cedula',
            'celular' => 'nullable|string|max:10',
            'correo' => 'required|string|email|max:100|unique:usuarios,correo',
            'nombre_usuario' => 'required|string|max:150|unique:usuarios,nombre_usuario',
            'contrasena' => 'required|string|min:6',
            'rol' => 'nullable|integer',
            'fecha_nac' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario = Usuario::create([
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'cedula' => $request->cedula,
            'celular' => $request->celular,
            'correo' => $request->correo,
            'nombre_usuario' => $request->nombre_usuario,
            'contrasena' => Hash::make($request->contrasena),
            'rol' => $request->rol ?? 8, // Default role (e.g. general user)
            'fecha_nac' => $request->fecha_nac,
        ]);

        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $usuario,
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

        // We override the credentials to check against the custom password column "contrasena"
        // tymon/jwt-auth / Laravel guard maps the credentials. Since we overrode getAuthPassword() in Usuario model,
        // we can authenticate by passing standard credentials where password is mapped to the internal field.
        // Wait, standard guard expects 'password' key to check the password.
        // So we must pass the contrasena credential as 'password' to the attempt method:
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
            'user' => $user->load('rolRelation'),
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile()
    {
        return response()->json(auth('api')->user()->load('rolRelation'));
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
