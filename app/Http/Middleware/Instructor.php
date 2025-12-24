<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Instructor
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks if the authenticated admin is an instructor
     * (has at least one course). If not, redirects to back dashboard.
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('back.login');
        }

        $admin = Auth::guard('admin')->user();

        // Check if admin is an instructor (has at least one course)
        if (!$admin->isInstructor()) {
            return redirect()->route('back.index')
                ->with('error', 'You must have at least one course to access instructor dashboard');
        }

        return $next($request);
    }
}
