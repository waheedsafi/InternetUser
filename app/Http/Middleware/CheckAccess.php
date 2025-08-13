<?php

namespace App\Http\Middleware;

use App\Enum\PermissionEnum;
use App\Enum\RoleEnum;
use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$params
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$params)
    {
        // Getting the authenticated user
        $user = auth()->user();

        // If user is not authenticated, deny access
        if (!$user) {
            abort(403, 'Access denied: not authenticated.');
        }

        // Initialize required permission and role variables
        $requiredPermission = null;
        $requiredRole = null;

        // Parsing params for permissions and roles
        foreach ($params as $param) {
            if (str_starts_with($param, 'permission=')) {
                $requiredPermission = str_replace('permission=', '', $param);
            }
            if (str_starts_with($param, 'role=')) {
                $requiredRole = str_replace('role=', '', $param);
            }
        }

        // Check if the role is specified and if the user has the correct role
        if ($requiredRole && $user->role_id !== RoleEnum::from($requiredRole)->value) {
            abort(403, 'Access denied: role mismatch.');
        }

        // Admin has unlimited access to everything
        if ($user->role_id === RoleEnum::Admin->value) {
            return $next($request);
        }

        // If a permission is required, check the user’s permission
        if ($requiredPermission) {

            // For Viewer: Only allowed to view system data and update their own profile
            if ($user->role_id === RoleEnum::viewer->value) {
                // Ensure Viewer can only view and update their own profile
                if (
                    ($requiredPermission === PermissionEnum::ViewUsers->value || $requiredPermission === PermissionEnum::UpdateUsers->value) 
                    && $request->route('id') == $user->id // Ensure viewer only views/updates own profile
                ) {
                    return $next($request);
                }

                // Viewer can view system data but cannot modify it
                if ($requiredPermission === PermissionEnum::ViewSystemData->value) {
                    return $next($request);
                }

                // Deny if the viewer tries to perform any other action
                abort(403, 'Access denied: viewer can only view and update own profile, and view system data.');
            }

            // For User: Allowed to perform actions on their own profile and some system data actions
            if ($user->role_id === RoleEnum::User->value) {
                $allowedPermissionsForUser = [
                    PermissionEnum::ViewUsers->value,           // View own profile
                    PermissionEnum::UpdateUsers->value,         // Update own profile
                    PermissionEnum::ViewSystemData->value,      // View system data
                    PermissionEnum::AddSystemData->value,       // Add system data
                    PermissionEnum::UpdateSystemData->value,    // Update system data
                    PermissionEnum::DeleteSystemData->value,    // Delete system data
                ];

                if (!in_array($requiredPermission, $allowedPermissionsForUser)) {
                    abort(403, 'Access denied: insufficient permission for user.');
                }

                return $next($request);
            }

            // Deny access if user doesn't have permission
            abort(403, 'Access denied: insufficient permission.');
        }

        // Proceed to the next middleware
        return $next($request);
    }
}
