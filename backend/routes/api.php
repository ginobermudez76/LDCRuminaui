<?php

use App\Http\Controllers\AprobarDenegarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartaCondolenciaController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DeporteController;
use App\Http\Controllers\DeportistaDestacadoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\LogroController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\RbacAdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudTipoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

const ROUTE_DEPORTES_ID = 'deportes/{id}';
const ROUTE_SOLICITUDES_ID = 'solicitudes/{id}';

// Public routes
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/accept-invitation', [AuthController::class, 'acceptInvitation']);

// Public read routes
Route::get('deportes', [DeporteController::class, 'index']);
Route::get(ROUTE_DEPORTES_ID, [DeporteController::class, 'show']);
Route::get('eventos', [EventoController::class, 'index']);
Route::get('noticias', [NoticiaController::class, 'index']);
Route::get('logros', [LogroController::class, 'index']);
Route::get('cartas', [CartaCondolenciaController::class, 'index']);
Route::get('cursos', [CursoController::class, 'index']);
Route::get('documentos', [DocumentoController::class, 'index']);
Route::get('deportistas', [DeportistaDestacadoController::class, 'index']);

// Protected routes (requires JWT but bypasses RBAC)
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('roles', [RoleController::class, 'index']);
});

// Protected routes (requires JWT and RBAC checks)
Route::group(['middleware' => ['auth:api', 'rbac']], function () {
    // Admin registration
    Route::post('auth/register', [AuthController::class, 'register']);

    // Solicitudes CRUD
    Route::get('solicitudes', [SolicitudController::class, 'index']);
    Route::get('solicitudes/asignadas', [SolicitudController::class, 'asignadas']);
    Route::post('solicitudes', [SolicitudController::class, 'store']);
    Route::get(ROUTE_SOLICITUDES_ID, [SolicitudController::class, 'show']);
    Route::put(ROUTE_SOLICITUDES_ID, [SolicitudController::class, 'update']);
    Route::delete(ROUTE_SOLICITUDES_ID, [SolicitudController::class, 'destroy']);
    Route::patch(ROUTE_SOLICITUDES_ID.'/reassign', [SolicitudController::class, 'reassign']);
    Route::post(ROUTE_SOLICITUDES_ID.'/procesar', [AprobarDenegarController::class, 'procesar']);

    // Rutas de Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
    Route::patch('/usuarios/{id}/toggle-active', [UsuarioController::class, 'toggleActive']);
    Route::post('/usuarios/{id}/reset-password', [UsuarioController::class, 'resetPassword']);
    Route::post('/usuarios/{id}/resend-invitation', [UsuarioController::class, 'resendInvitation']);

    // Solicitud Tipos CRUD (Dynamic Workflows)
    Route::apiResource('solicitud-tipos', SolicitudTipoController::class);

    // Publicista - Deportes
    Route::post('deportes', [DeporteController::class, 'store']);
    Route::put(ROUTE_DEPORTES_ID, [DeporteController::class, 'update']);
    Route::delete(ROUTE_DEPORTES_ID, [DeporteController::class, 'destroy']);

    // Publicista - Eventos
    Route::post('eventos', [EventoController::class, 'store']);
    Route::put('eventos/{id}', [EventoController::class, 'update']);
    Route::delete('eventos/{id}', [EventoController::class, 'destroy']);

    // Publicista - Noticias
    Route::post('noticias', [NoticiaController::class, 'store']);
    Route::put('noticias/{id}', [NoticiaController::class, 'update']);
    Route::delete('noticias/{id}', [NoticiaController::class, 'destroy']);

    // Publicista - Logros
    Route::post('logros', [LogroController::class, 'store']);
    Route::put('logros/{id}', [LogroController::class, 'update']);
    Route::delete('logros/{id}', [LogroController::class, 'destroy']);

    // Publicista - Cursos
    Route::post('cursos', [CursoController::class, 'store']);
    Route::put('cursos/{id}', [CursoController::class, 'update']);
    Route::delete('cursos/{id}', [CursoController::class, 'destroy']);

    // Publicista - Documentos
    Route::post('documentos', [DocumentoController::class, 'store']);
    Route::put('documentos/{id}', [DocumentoController::class, 'update']);
    Route::delete('documentos/{id}', [DocumentoController::class, 'destroy']);

    // Publicista - Deportistas Destacados
    Route::post('deportistas', [DeportistaDestacadoController::class, 'store']);
    Route::put('deportistas/{id}', [DeportistaDestacadoController::class, 'update']);
    Route::delete('deportistas/{id}', [DeportistaDestacadoController::class, 'destroy']);

    // Publicista - Cartas de Condolencia
    Route::post('cartas', [CartaCondolenciaController::class, 'store']);
    Route::put('cartas/{id}', [CartaCondolenciaController::class, 'update']);
    Route::delete('cartas/{id}', [CartaCondolenciaController::class, 'destroy']);

    // RBAC Configuration Management
    Route::get('rbac/roles', [RbacAdminController::class, 'listRoles']);
    Route::post('rbac/roles', [RbacAdminController::class, 'storeRole']);
    Route::put('rbac/roles/{id}', [RbacAdminController::class, 'updateRole']);
    Route::delete('rbac/roles/{id}', [RbacAdminController::class, 'destroyRole']);
    Route::post('rbac/roles/{id}/opciones', [RbacAdminController::class, 'syncRoleOptions']);

    Route::get('rbac/opciones', [RbacAdminController::class, 'listOptions']);
    Route::post('rbac/opciones', [RbacAdminController::class, 'storeOption']);
    Route::put('rbac/opciones/{id}', [RbacAdminController::class, 'updateOption']);
    Route::delete('rbac/opciones/{id}', [RbacAdminController::class, 'destroyOption']);
    Route::post('rbac/opciones/{id}/endpoints', [RbacAdminController::class, 'syncOptionEndpoints']);

    Route::get('rbac/endpoints', [RbacAdminController::class, 'listEndpoints']);
    Route::post('rbac/endpoints', [RbacAdminController::class, 'storeEndpoint']);
    Route::put('rbac/endpoints/{id}', [RbacAdminController::class, 'updateEndpoint']);
    Route::delete('rbac/endpoints/{id}', [RbacAdminController::class, 'destroyEndpoint']);
});
