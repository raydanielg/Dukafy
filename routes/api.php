<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::get('/managers/search', [App\Http\Controllers\Api\AuthController::class, 'searchManagers']);
        Route::post('/approve-initial', [App\Http\Controllers\Api\AuthController::class, 'approveInitial']);
        Route::post('/complete-onboarding', [App\Http\Controllers\Api\AuthController::class, 'completeOnboarding']);
        Route::delete('/delete-account', [App\Http\Controllers\Api\AuthController::class, 'deleteAccount']);
        Route::post('/update-profile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
        Route::post('/update-business-logo', [App\Http\Controllers\Api\AuthController::class, 'updateBusinessLogo']);
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    });
});
