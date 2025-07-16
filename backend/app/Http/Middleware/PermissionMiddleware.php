<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Permission Middleware
 * 
 * Handles permission-based access control
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::user();

        if (!$user || !$user->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này',
                'code' => 'AUTH_008'
            ], 403);
        }

        return $next($request);
    }
}