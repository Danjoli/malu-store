<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\Admin;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['email' => 'Muitas tentativas. Aguarde um minuto antes de tentar novamente.'])->onlyInput('email');
        }

        if (! Auth::guard('admin')->attempt($credentials + ['is_active' => true])) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors([
                    'email' => 'Credenciais inválidas.',
                ])
                ->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (config('admin_security.two_factor_enabled')) {
            Auth::guard('admin')->logout();
            $request->session()->regenerate();

            $code = (string) random_int(100000, 999999);
            $request->session()->put('admin_two_factor', [
                'admin_id' => $admin->id,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes((int) config('admin_security.two_factor_ttl_minutes'))->timestamp,
                'attempts' => 0,
            ]);

            try {
                $admin->sendTwoFactorCode($code);
            } catch (Throwable) {
                $request->session()->forget('admin_two_factor');

                return back()->withErrors([
                    'email' => 'Não foi possível enviar o código de segurança. Tente novamente em instantes.',
                ])->onlyInput('email');
            }

            return redirect()->route('admin.two-factor.challenge');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showTwoFactorChallenge(Request $request)
    {
        abort_unless($request->session()->has('admin_two_factor'), 404);

        return view('admin.auth.two-factor-challenge');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);
        $pending = $request->session()->get('admin_two_factor');

        if (! is_array($pending) || (int) ($pending['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget('admin_two_factor');

            return redirect()->route('admin.login')->withErrors([
                'email' => 'O código expirou. Entre novamente para receber outro.',
            ]);
        }

        $attempts = (int) ($pending['attempts'] ?? 0) + 1;
        if ($attempts > (int) config('admin_security.two_factor_max_attempts')) {
            $request->session()->forget('admin_two_factor');

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Limite de tentativas atingido. Entre novamente.',
            ]);
        }

        if (! Hash::check($request->string('code')->toString(), (string) ($pending['code_hash'] ?? ''))) {
            $pending['attempts'] = $attempts;
            $request->session()->put('admin_two_factor', $pending);

            return back()->withErrors(['code' => 'Código inválido.']);
        }

        $admin = Admin::query()->whereKey($pending['admin_id'] ?? null)->where('is_active', true)->first();
        if (! $admin) {
            $request->session()->forget('admin_two_factor');

            return redirect()->route('admin.login')->withErrors(['email' => 'Conta administrativa indisponível.']);
        }

        $request->session()->forget('admin_two_factor');
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::broker('admins')->sendResetLink($request->only('email'));

        return back()->with('success', 'Se existir uma conta administrativa com este e-mail, enviaremos um link para redefinir a senha.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('admin.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['token' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', 'confirmed', ...StrongPassword::rules()]]);

        $status = Password::broker('admins')->reset($request->only('email', 'password', 'password_confirmation', 'token'), function (Admin $admin, string $password) {
            $admin->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', 'Senha atualizada. Entre com sua nova senha.')
            : back()->withErrors(['email' => [__($status)]]);
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
