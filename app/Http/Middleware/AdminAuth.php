<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('admin_authed', false)) {
            return redirect()->route('admin.login.form');
        }

        return $next($request);
    }
}
