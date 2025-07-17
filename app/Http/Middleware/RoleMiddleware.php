<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has the required role
        if ($user->role !== $role) {
            // Log unauthorized access attempt
            Transaction::log(
                'security_event',
                'Unauthorized role access attempt',
                "User {$user->name} ({$user->role}) attempted to access {$role}-only resource: {$request->path()}",
                [
                    'required_role' => $role,
                    'user_role' => $user->role,
                    'path' => $request->path(),
                    'method' => $request->method(),
                ],
                $user,
                null,
                Transaction::SEVERITY_WARNING
            );
            
            // Return appropriate response based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions. This action requires ' . $role . ' role.',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 
                'Access denied. This page is only available to ' . ucfirst($role) . 's.');
        }
        
        return $next($request);
    }
}
