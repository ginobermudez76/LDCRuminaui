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

// Public routes
Route::post('auth/login', [AuthController::class, 'login']);

// Public read routes
Route::get('deportes', [DeporteController::class, 'index']);
Route::get('eventos', [EventoController::class, 'index']);
Route::get('noticias', [NoticiaController::class, 'index']);
Route::get('logros', [LogroController::class, 'index']);

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

    // Solicitud Tipos CRUD (Dynamic Workflows)
    Route::apiResource('solicitud-tipos', SolicitudTipoController::class);

    // Admin/Publicist management routes
    Route::apiResource('deportes', DeporteController::class)->except(['index', 'show']);
    Route::apiResource('eventos', EventoController::class)->except(['index', 'show']);
    Route::apiResource('noticias', NoticiaController::class)->except(['index', 'show']);
    Route::apiResource('logros', LogroController::class)->except(['index', 'show']);
});
