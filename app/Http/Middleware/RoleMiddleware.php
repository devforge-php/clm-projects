<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Agar foydalanuvchining roli berilgan rolga mos kelmasa, 403 qaytaramiz
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Bu sahifaga kirish mumkin emas.');
        }

        return $next($request);
    }
}
