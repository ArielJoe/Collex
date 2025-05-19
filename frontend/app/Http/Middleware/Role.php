<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Role
{
    public function handle(Request $request, Closure $next)
    {
        // Get the user's role from session
        $role = session('role');

        // Check if the user is authenticated
        // if (!session('role')) {
        //     return redirect()->route('login')->withErrors(['unauthenticated' => 'Please login first!']);
        // }

        // Get the current route's first path segment (e.g., 'member' from '/member/dashboard')
        $pathSegment = explode('/', $request->path())[0];

        // Define valid roles
        $validRoles = ['member', 'organizer', 'finance', 'admin'];

        // Check if role exists and matches the path segment
        if (!$role || !in_array($role, $validRoles) || $role !== $pathSegment) {
            return redirect('/401');
        }

        // Proceed with the request if role matches path
        return $next($request);
    }
}
