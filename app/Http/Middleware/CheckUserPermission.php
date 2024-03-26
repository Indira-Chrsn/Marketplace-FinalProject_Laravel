<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckUserPermission
{
    public function handle($request, $next, $action, $modelClass)
    {
        $user = $request->user();

        // Permission check based on action and model
        $permission = "{$action} " . strtolower(class_basename($modelClass));
        if (!$user->can($permission)) {
            return abort(403, 'Unauthorized'); // Deny access
        }

        // Ownership check for update and delete actions
        if (in_array($action, ['create', 'update', 'delete'])) {
            $modelInstance = $modelClass::findOrFail($request->route()->parameter($modelClass::singular()));
            if ($modelInstance->user_id !== $user->id) {
                return abort(403, 'Unauthorized'); // Deny access if not owner
            }
        }

        return $next($request);
    }
}