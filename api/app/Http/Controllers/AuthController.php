<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(
            [
                'name' => "required",
                'email' => "required|email|unique:users,email",
                'password' => "required|min:8|confirmed"
            ],

            [
                'name.required' => "É necessário colocar o nome",
                "email.required" => "É necessário colocar um e-mail",
                "email.email" => "É necessário colocar um e-mail válido",
                "email.unique" => "Este e-mail não está disponível",
                "password.unique" => "É necessário inserir uma senha",
                "password.min" => "O minímo é :min caracter",
                "password.confirmed" => "As senhas não coincidem. Tente novamente.",
                "password.required" => "É necessário colocar a senha",
            ]

        );



        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Dispara o e-mail de verificação
        $user->sendEmailVerificationNotification();

        return response()->json(
            [
                'status' => 'ok',
                'message' => 'Usuário criado com sucesso!! Verifique seu e-mail para ativar a conta.'
            ]
        );
        //return $user;
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => "required|email",
                'password' => "required"
            ],
            [
                'email.required' => 'Necessário inserir um email',
                'email.email' => 'Necessário inserir um email válido',
                'password.required' => 'Necessário inserir uma senha',
            ]
        );

        $user = auth()->attempt(['email' => $request->email, 'password' => $request->password]);
        

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Credenciais não encontrada'
                ],
                401
            );
        }

        $user_verifid = auth()->user();

        // Se não verificou email, bloqueia
        if (!$user_verifid->hasVerifiedEmail()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'É necessário verificar seu e-mail antes de entrar.'
                ],
                403 // Forbidden
            );
        }


        $token = auth()->user()->createToken(auth()->user()->name)->plainTextToken;

        return response()->json(
            [
                'status' => 'ok',
                'token' => $token
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Não exponha se o e-mail existe: resposta sempre genérica
        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'Se este e-mail existir, enviaremos um link para redefinição.',
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Senha redefinida com sucesso.']);
        }

        return response()->json([
            'message' => 'Não foi possível redefinir a senha com os dados fornecidos.'
        ], 422);
    }
}
