<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Automatically check and transition expired pending invitations
        Usuario::where('invitation_status', 'pendiente')
            ->where('invitation_expires_at', '<', now())
            ->update(['invitation_status' => 'expirada']);

        // Get all active users with their roles loaded
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
            'correo_electronico' => 'required|email|max:100|unique:usuario,correo_electronico',
            'fecha_nac' => 'required|date',
            'rol_id' => 'required|integer|exists:rol,id',
            'username' => 'required|string|unique:usuario,nombre_usuario',
            'foto_perfil' => 'nullable|file|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Construct names
            $nombres = trim($validatedData['nombre'] . ' ' . ($validatedData['snombre'] ?? ''));
            $apellidos = trim($validatedData['apellido'] . ' ' . ($validatedData['sapellido'] ?? ''));

            // Auto-generate invitation token
            $token = Str::random(40);
            $expiresAt = now()->addDays(7);

            // Create user (initially inactive)
            $usuario = new Usuario();
            $usuario->uuid = (string) Str::uuid();
            $usuario->nombre_usuario = $validatedData['username'];
            $usuario->correo_electronico = $validatedData['correo_electronico'];
            // Temporary random password
            $usuario->password_hash = Hash::make(Str::random(16));
            $usuario->nombres = $nombres;
            $usuario->apellidos = $apellidos;
            $usuario->cedula = $validatedData['cedula'];
            $usuario->celular = $validatedData['celular'];
            $usuario->fecha_nac = $validatedData['fecha_nac'];
            $usuario->activo = 0; // Inactive until invitation accepted
            $usuario->deleted = 0;
            $usuario->invitation_token = $token;
            $usuario->invitation_expires_at = $expiresAt;
            $usuario->invitation_status = 'pendiente';

            if ($request->hasFile('foto_perfil')) {
                $path = $request->file('foto_perfil')->store('profiles', 'public');
                $usuario->foto_perfil = '/storage/' . $path;
            }

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

            // Log invitation email sending
            $invitationLink = "http://localhost:4200/accept-invitation?token=" . $token;
            Log::info("Invitación enviada a {$usuario->correo_electronico}. Link: {$invitationLink}");

            $usuario->load('roles');

            return response()->json([
                'user' => $usuario,
                'invitation_link' => $invitationLink
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear el usuario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::where('deleted', 0)->findOrFail($id);

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:45',
            'snombre' => 'nullable|string|max:45',
            'apellido' => 'required|string|max:45',
            'sapellido' => 'nullable|string|max:45',
            'cedula' => 'required|string|max:10',
            'celular' => 'nullable|string|max:10',
            'correo_electronico' => 'required|email|max:100|unique:usuario,correo_electronico,' . $id,
            'fecha_nac' => 'required|date',
            'rol_id' => 'required|integer|exists:rol,id',
            'foto_perfil' => 'nullable|file|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $nombres = trim($validatedData['nombre'] . ' ' . ($validatedData['snombre'] ?? ''));
            $apellidos = trim($validatedData['apellido'] . ' ' . ($validatedData['sapellido'] ?? ''));

            $usuario->correo_electronico = $validatedData['correo_electronico'];
            $usuario->nombres = $nombres;
            $usuario->apellidos = $apellidos;
            $usuario->cedula = $validatedData['cedula'];
            $usuario->celular = $validatedData['celular'];
            $usuario->fecha_nac = $validatedData['fecha_nac'];

            if ($request->hasFile('foto_perfil')) {
                if ($usuario->foto_perfil) {
                    $oldPath = str_replace('/storage/', '', $usuario->foto_perfil);
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('foto_perfil')->store('profiles', 'public');
                $usuario->foto_perfil = '/storage/' . $path;
            }

            $usuario->save();

            // Update role assignment in rol_usuario table
            // Set previous roles as deleted
            DB::table('rol_usuario')
                ->where('id_usuario', $usuario->id)
                ->update(['deleted' => 1, 'updated_at' => now()]);

            // Add new role
            DB::table('rol_usuario')->insert([
                'uuid' => (string) Str::uuid(),
                'id_rol' => $validatedData['rol_id'],
                'id_usuario' => $usuario->id,
                'deleted' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            $usuario->load('roles');

            return response()->json($usuario);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo actualizar el usuario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource (soft delete).
     */
    public function destroy($id)
    {
        $usuario = Usuario::where('deleted', 0)->findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $usuario->deleted = 1;
            $usuario->deleted_at = now();
            $usuario->activo = 0;
            $usuario->save();

            // Delete role associations too
            DB::table('rol_usuario')
                ->where('id_usuario', $usuario->id)
                ->update(['deleted' => 1, 'updated_at' => now()]);

            DB::commit();
            return response()->json(['message' => 'Usuario eliminado con éxito (soft delete)']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo eliminar el usuario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle the active state of a user.
     */
    public function toggleActive($id)
    {
        $usuario = Usuario::where('deleted', 0)->findOrFail($id);
        $usuario->activo = $usuario->activo ? 0 : 1;
        $usuario->save();

        return response()->json([
            'message' => 'Estado de usuario modificado', 
            'activo' => $usuario->activo
        ]);
    }

    /**
     * Reset password to an autogenerated value and send mock email.
     */
    public function resetPassword($id)
    {
        $usuario = Usuario::where('deleted', 0)->findOrFail($id);
        
        // Generate a 12-character secure password
        $password = Str::random(12);
        
        $usuario->password_hash = Hash::make($password);
        $usuario->save();

        // Log sending the reset email
        Log::info("Contraseña reestablecida para {$usuario->correo_electronico}. Nueva contraseña temporal: {$password}");

        return response()->json([
            'message' => 'Contraseña reestablecida con éxito. La nueva contraseña ha sido enviada al correo.',
            'generated_password' => $password
        ]);
    }

    /**
     * Resend user invitation.
     */
    public function resendInvitation($id)
    {
        $usuario = Usuario::where('deleted', 0)->findOrFail($id);
        
        if ($usuario->invitation_status === 'aceptada') {
            return response()->json(['error' => 'Este usuario ya ha aceptado su invitación'], 400);
        }

        $token = Str::random(40);
        $expiresAt = now()->addDays(7);

        $usuario->invitation_token = $token;
        $usuario->invitation_expires_at = $expiresAt;
        $usuario->invitation_status = 'pendiente';
        $usuario->activo = 0;
        $usuario->save();

        $invitationLink = "http://localhost:4200/accept-invitation?token=" . $token;
        Log::info("Reenvío de invitación para {$usuario->correo_electronico}. Link: {$invitationLink}");

        return response()->json([
            'message' => 'Invitación reenviada con éxito.',
            'invitation_link' => $invitationLink
        ]);
    }
}

