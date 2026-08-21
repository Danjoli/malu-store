<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Auth\LoginRequest;
use App\Http\Requests\Public\Auth\RegisterRequest;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('public.auth.login');
    }

    public function showRegister()
    {
        return view('public.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect('/')
            ->with('success', 'Conta criada com sucesso! Bem-vindo, '.$user->name);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $throttleKey = Str::lower($data['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['email' => 'Muitas tentativas. Aguarde um minuto antes de tentar novamente.'])->onlyInput('email');
        }

        $login = $data['email'];

        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        if (Auth::attempt([
            $field => $login,
            'password' => $data['password'],
        ])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect('/')
                ->with('success', 'Login realizado com sucesso!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->with('error', 'Email, telefone ou senha incorretos')
            ->withInput();
    }

    public function showForgotPassword()
    {
        return view('public.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::broker('users')->sendResetLink($request->only('email'));

        return back()->with('success', 'Se existir uma conta com este e-mail, enviaremos um link para redefinir a senha.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('public.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', ...StrongPassword::rules()],
        ]);

        $status = Password::broker('users')->reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Senha atualizada. Entre com sua nova senha.')
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Você saiu da sua conta com sucesso');
    }
}
