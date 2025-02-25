<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TimesheetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');


// Protected routes (require user token)
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // User self-management routes (must come before the resource routes)
    Route::get('/users/me', [UserController::class, 'showSelf']);
    Route::put('/users/me', [UserController::class, 'updateSelf']);

    // Project routes
    Route::apiResource('projects', ProjectController::class);

    // Timesheet routes
    Route::apiResource('timesheets', TimesheetController::class);
});

// Client credentials protected routes
Route::middleware(['client'])->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::apiResource('users', UserController::class);
    Route::apiResource('attributes', AttributeController::class);
});
