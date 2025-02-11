<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdminOrLader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('user')->check()) {
            $user = Auth::guard('user')->user();
            if ($user->role == 'lader') {
                return $next($request);
            }
        }

        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if ($admin->role == 'admin' || $admin->role == 'super_admin') {
                return $next($request);
            }
        }
        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
