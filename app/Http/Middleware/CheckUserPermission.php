<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckUserPermission
{
    public function handle($request, $next, $action, $modelClass)
    {
        $user = $request->user();

        $modelInstance = $modelClass::findOrFail($request->route()->parameter($modelClass::singular()));
        if ($modelInstance->user_id !== $user->id) {
            return abort(403, 'Unauthorized'); // Deny access if not owner
        }

        return $next($request);
    }
}