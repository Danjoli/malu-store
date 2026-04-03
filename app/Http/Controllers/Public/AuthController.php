<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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


    public function register(Request $request)
    {

        $request->validate(
        [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:10|unique:users',
            'password' => 'required|min:6'
        ],
        [
            'name.required' => 'O nome é obrigatório',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres',

            'email.required' => 'O email é obrigatório',
            'email.email' => 'Digite um email válido',
            'email.unique' => 'Este email já está cadastrado',

            'phone.required' => 'O telefone é obrigatório',
            'phone.unique' => 'Este telefone já está cadastrado',

            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres'
        ]);


        $user = User::create(
        [
            'name' => $request->name,

            'email' => $request->email,

            'phone' => $request->phone,

            'password' => Hash::make($request->password)
        ]);


        Auth::login($user);


        return redirect('/')
            ->with('success', 'Conta criada com sucesso! Bem-vindo, ' . $user->name);


    }



    public function login(Request $request)
    {

        $request->validate(
        [
            'email' => 'required',
            'password' => 'required'
        ],
        [
            'email.required' => 'Informe seu email ou telefone',
            'password.required' => 'Informe sua senha'
        ]);


        $login = $request->email;


        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';


        if(Auth::attempt([$field => $login, 'password' => $request->password]))
        {
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
