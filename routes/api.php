<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'store']);

Route::post('/login', [UserController::class, 'login']);




Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    
    Route::put('/users/{user}', [UserController::class, 'update']);
    
    Route::get('/students', [StudentController::class, 'index']);
    
    Route::post('/students', [StudentController::class, 'store']);
    
    Route::get('/students/{student}', [StudentController::class, 'show']);
    
    Route::put('/students/{student}', [StudentController::class, 'update']);
    
    Route::delete('/students/{student}', [StudentController::class, 'destroy']);
    
    Route::post('/students/{student}/restore', [StudentController::class, 'restore']);

    Route::delete('/users/{user}', [UserController::class, 'destroy']);

});