<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;



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

    //Função USER
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
});

// //Função anônima para pegar as informações do usuário
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user(); // retorna os dados do usuário logado
// });

// Notificação para enviar a  verificação de e-mail
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verification link sent!');
})->middleware(['throttle:6,1'])->name('verification.send');

// Envia o link para verificar o e-mail
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json(['message' => 'Link de verificação inválido.'], 400);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect(config('app.frontend_url') . '/emailConfirmation');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

// Verifica e reenvia o e-mail
Route::post('/email/resend-guest', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Usuário não encontrado.'
        ], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json([
            'status' => 'ok',
            'message' => 'Este e-mail já foi verificado.'
        ]);
    }

    $user->sendEmailVerificationNotification();

    return response()->json([
        'status' => 'ok',
        'message' => 'Link de verificação reenviado.'
    ]);
});


// Registro de usuário
Route::post('/register', [AuthController::class, 'register'])->name('register');

//Login de usuário
Route::post('/login', [AuthController::class, 'login'])->name('login');

//Forgot Password
Route::post('/forgotPassword', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
//Reset Password
Route::post('/resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');
