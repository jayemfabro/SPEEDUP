<?php

namespace App\Http\Middleware;

use App\Models\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!$request->user()) {
            Log::error('User not authenticated');
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Convert the required role to its string value if it's an enum
        $requiredRole = $role instanceof UserRole ? $role->value : $role;
        
        // Convert user's role to string if it's an enum
        $userRole = $user->role instanceof UserRole ? $user->role->value : $user->role;
        
        Log::info('Role check', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'required_role' => $requiredRole
        ]);

        if ($userRole !== $requiredRole) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'required_role' => $requiredRole
            ]);
            
            // Check if request expects JSON (API request)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden: this action requires '.ucfirst($requiredRole).' privileges.'
                ], 403);
            }
            
            // Render custom 403 error page for web requests
            return Inertia::render('Errors/403', [
                'role' => ucfirst($requiredRole)
            ])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}