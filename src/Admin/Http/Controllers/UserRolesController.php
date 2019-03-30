<?php
namespace Reflexions\Content\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Reflexions\Content\Admin\Flash;
use Reflexions\Content\Models\Role;
use View;
use Content;
use Redirect;
use Config;

class UserRolesController extends Controller
{
    public function editAll(Request $request)
    {
        $config_name = $this->getConfigNameFromUrl($request);

        $roles = Role::withoutSuperAdmin()->get();

        if (!count($roles)) {
            Flash::error('Error!', 'Add roles in order to bulk edit / edit all');
            return Redirect::route('admin-role-create');
        }

        $permissions = Config::get('content.permissions');

        return View::make(Content::package() . "::admin.roles.edit-all", compact('roles', 'permissions', 'config_name'));
    }

    public function updateAll(Request $request)
    {
        $config_name = $this->getConfigNameFromUrl($request);

        $permissions = $request->request->get('permissions', []);

        Role::withoutSuperAdmin()->pluck('name')
            ->mapWithKeys(function ($role) use ($permissions) {
                if (array_key_exists($role, $permissions)) {
                    return [$role => $permissions[$role]];
                }

                return [$role => ''];
            })
            ->each(function ($permissions, $role) {
                $role = Role::where('name', $role)->first();

                if ($role) {
                    $role->permissions = json_encode($permissions);
                    $role->save();
                }
            });

        Flash::success('Success!', 'Roles successfully updated');
        return Redirect::route("admin-{$config_name}-edit-all");
    }

    private function getConfigNameFromUrl(Request $request)
    {
        return $request->segment(2) ?: 'roles';
    }
}