<?php

namespace Reflexions\Content\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Reflexions\Content\Admin\Flash;

class CheckAllowedPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        $permissions = explode('|', $permissions);
        $allowed = false;

        if (Auth::check()) {
            if (Auth::user()->isSuperAdmin()) return $next($request);

            foreach ($permissions as $permission) {
                if (Auth::user()->hasPermission($permission)) {
                    $allowed = true;
                }
            }
        }

        if ($allowed) {
            return $next($request);
        }

        Flash::error('Error!', 'Permission denied');
        return Redirect::to('/admin');
    }
}