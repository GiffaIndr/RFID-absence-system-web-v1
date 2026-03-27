<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (!$request->user() || !$request->user()->hasRole($role)) {
            // Kalau bukan role yang sesuai, redirect ke dashboard sendiri
            if ($request->user()?->hasRole('hrd')) {
                return redirect()->route('hrd.dashboard');
            }
            return redirect()->route('karyawan.dashboard');
        }

        return $next($request);
    }
}
