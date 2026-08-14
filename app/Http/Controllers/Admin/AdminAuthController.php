<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        if (! Auth::guard('admin')->attempt($request->validated())) {
            return back()
                ->withErrors([
                    'email' => 'Credenciais inválidas.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Rotaciona a sessão sem apagar uma eventual autenticação do guard web.
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
