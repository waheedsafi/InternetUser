<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authmidd
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized. Please log in first.'
            ], 401);
        }

        $user = Auth::user();

        
        if ($role && $user->role !== $role && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Forbidden. You do not have access to this resource.'
            ], 403);
        }

       
        return $next($request);
    }
}
