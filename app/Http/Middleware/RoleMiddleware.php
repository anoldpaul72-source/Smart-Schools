<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this portal.');
        }

        $user = Auth::user();

        // Normalize leadership roles so Head of School, Headmaster, and Headmistress are interchangeable
        $equivalentRoles = match ($user->role) {
            'Head of School', 'Head Of School', 'Headmaster', 'Headmistress' => ['Head of School', 'Head Of School', 'Headmaster', 'Headmistress'],
            default => [$user->role],
        };

        if (empty(array_intersect($equivalentRoles, $roles))) {
            abort(403, 'Unauthorized access for your account role (' . $user->role . ').');
        }

        return $next($request);
    }
}
