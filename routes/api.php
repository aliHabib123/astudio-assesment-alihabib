<?php

use App\Http\Controllers\Api\AuthController;
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

// Client credentials protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
});

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected routes (require user token)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
});
