<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectStagingEnvironment
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('staging')) {
            return $next($request);
        }

        $expectedUser = (string) config('staging.basic_auth.username');
        $expectedPassword = (string) config('staging.basic_auth.password');
        $authorized = $expectedUser !== ''
            && $expectedPassword !== ''
            && hash_equals($expectedUser, (string) $request->getUser())
            && hash_equals($expectedPassword, (string) $request->getPassword());

        if (! $authorized) {
            return response('Ambiente de homologação protegido.', 401, [
                'WWW-Authenticate' => 'Basic realm="Malu Store Homologacao"',
                'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            ]);
        }

        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        return $response;
    }
}
