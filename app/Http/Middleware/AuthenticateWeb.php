<?php

namespace App\Http\Middleware;

use Closure;
use App\View\Components\Notification;
use App\Helpers\SysUtils;

class AuthenticateWeb
{
    public function handle($request, Closure $next)
    {
        if (!SysUtils::isLoggedIn()) {
            return $this->redirectNoPermission();
        }

        // all good
        return $next($request);
    }

    private function redirectNoPermission()
    {
        return redirect()
            ->route('site.login')
            ->withErrors(['msg' => 'Você não tem acesso a esse conteúdo! Faça o login novamente.']);
    }
}
