<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InstructorUser
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks if the authenticated user is an instructor
     * (type === 'instructor'). If not, redirects to courses page.
     * 
     * Note: New instructors may not have courses yet, so no course count check is needed.
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('front.login');
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if user is an instructor
        if (!$user || !$user->isInstructor()) {
            return redirect()->route('front.courses.index')
                ->with('error', __('lang.only_instructors_can_access') ?? 'Only instructors can access this page.');
        }

        return $next($request);
    }
}
