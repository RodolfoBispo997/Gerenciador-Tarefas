<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->group(function () {
    //Chamada de Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('task', TaskController::class);

    // Dashboard
    Route::get('/taskStatus', [TaskController::class, 'status']);
    Route::get('/taskByUser', [TaskController::class, 'tasksPerUser']);
    Route::get('/taskByUserAndStatus', [TaskController::class, 'tasksByUserAndStatus']);
    Route::get('/taskPercentageCompleted', [TaskController::class, 'PercentageOfConclusion']);
    Route::get('/top5', [TaskController::class, 'top5']);
});



// Registro de usuário
Route::post('/register', [AuthController::class, 'register'])->name('register');

//Login de usuário
Route::post('/login', [AuthController::class, 'login'])->name('login');

//Forgot Password
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
//Reset Password
Route::post('/resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');
