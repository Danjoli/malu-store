<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Auth\RegisterRequest;
use App\Http\Requests\Public\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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
            ->with('success', 'Conta criada com sucesso! Bem-vindo, ' . $user->name);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $login = $data['email'];

        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        if (Auth::attempt([
            $field => $login,
            'password' => $data['password']
        ])) {
            $request->session()->regenerate();

            return redirect('/')
                ->with('success', 'Login realizado com sucesso!');
        }

        return back()
            ->with('error', 'Email, telefone ou senha incorretos')
            ->withInput();
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
