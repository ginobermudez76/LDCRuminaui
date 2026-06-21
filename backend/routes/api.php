<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\DeporteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\LogroController;
use App\Http\Controllers\SolicitudTipoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AprobarDenegarController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DeportistaDestacadoController;
use App\Http\Controllers\CartaCondolenciaController;

// Public routes
Route::post('auth/login', [AuthController::class, 'login']);

// Public read routes
Route::get('deportes', [DeporteController::class, 'index']);
Route::get('deportes/{id}', [DeporteController::class, 'show']);
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
    Route::get('solicitudes/{id}', [SolicitudController::class, 'show']);
    Route::put('solicitudes/{id}', [SolicitudController::class, 'update']);
    Route::delete('solicitudes/{id}', [SolicitudController::class, 'destroy']);
    Route::patch('solicitudes/{id}/reassign', [SolicitudController::class, 'reassign']);
    Route::post('solicitudes/{id}/procesar', [AprobarDenegarController::class, 'procesar']);

    // Rutas de Usuarios
    Route::get('/usuarios', [App\Http\Controllers\UsuarioController::class, 'index']);
    Route::post('/usuarios', [App\Http\Controllers\UsuarioController::class, 'store']);

    // Solicitud Tipos CRUD (Dynamic Workflows)
    Route::apiResource('solicitud-tipos', SolicitudTipoController::class);

    // Publicista - Deportes
    Route::post('deportes', [DeporteController::class, 'store']);
    Route::put('deportes/{id}', [DeporteController::class, 'update']);
    Route::delete('deportes/{id}', [DeporteController::class, 'destroy']);

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
});
