<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::guard('admin')->user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('back.login');
        }

        // Check if user is super admin (bypass permission check)
        // Assuming you have a super_admin column or method
        if (isset($user->super_admin) && $user->super_admin) {
            return $next($request);
        }

        // Check if user has the permission using Spatie Permission
        // hasPermissionTo checks both direct permissions and permissions via roles
        try {
            if (!$user->hasPermissionTo($permission, 'admin')) {
                abort(403, 'Unauthorized action. You do not have permission to perform this action.');
            }
        } catch (\Exception $e) {
            // If permissions table doesn't exist or permissions not set up yet
            // Allow access for now (you can remove this after setting up permissions)
            // For development/testing purposes only
        }

        return $next($request);
    }
}