<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('revoke', [AuthController::class, 'revoke']);
        Route::post('revoke-all', [AuthController::class, 'revokeAll']);
    });
    Route::apiResource('audit', AuditController::class)->only(['index', 'show']);

    Route::prefix('roles')->group(function () {
        Route::get('lookup', [RoleController::class, 'lookup']);
        Route::put('{id}/permissions', [RoleController::class, 'assignPermission']);
    });
    Route::apiResource('roles', RoleController::class);

    Route::prefix('permissions')->group(function () {
        Route::get('lookup', [PermissionController::class, 'lookup']);
    });

    Route::prefix('states')->group(function () {
        Route::get('lookup', [StateController::class, 'lookup']);
    });

    Route::prefix('persons')->group(function () {
        Route::get('lookup', [PersonController::class, 'lookup']);
    });
    Route::apiResource('persons', PersonController::class);

    Route::prefix('users')->group(function () {
        Route::get('lookup', [UserController::class, 'lookup']);
        Route::put('{id}/roles', [UserController::class, 'assignRole']);
    });
    Route::apiResource('users', UserController::class);

    Route::apiResource('files', FileController::class);
    Route::prefix('files')->group(function () {
        Route::get('{id}/download', [FileController::class, 'download']);
    });
});
