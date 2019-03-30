<?php
namespace Reflexions\Content\Models\Admin;

use Auth;
use Reflexions\Content\Admin\AdminOptions;
use Datatables;
use URL;
use Reflexions\Content\Models\Role;
use Config;
use Route;
use View;
use Content;

class UserRoleAdminOptions extends AdminOptions {

    public function label(){
        return 'Role';
    }

    public function pageIcon() {
        return "fa-user-plus";
    }

    // ------------------------------------
    //   List page options
    // ------------------------------------
    public function query()
    {
        $roles = Role::select('id', 'name');
        if (!Auth::user()->hasRole('SuperAdmin')) {
            $roles = $roles->where('name', '!=', 'SuperAdmin');
        }
        return $roles;
    }

    public function datatables()
    {
        $query = $this->query();
        $datatables = Datatables::of($query);
        return $datatables;
    }

    public function tableColumns()
    {
        return [
            $this->column('id', 'ID'),
            $this->column('name', 'Name'),
            $this->actionColumn('Actions'),
        ];
    }

    public function listActions() {
        return [
            $this->action('Export', 'fa-download', URL::route('admin-'.$this->name().'-export')),
            $this->action('Create', 'fa-plus', URL::route('admin-'.$this->name().'-create')),
            $this->action('Edit All', 'fa-pencil', URL::route('admin-'.$this->name().'-edit-all'))
        ];
    }

    public function rowActions($row)
    {
        return [
            $this->action('Edit', 'fa fa-edit', URL::route('admin-'.$this->name().'-edit', $row->id)),
        ];
    }

    public function order() {
        return [[1, "asc"]];
    }

    // ------------------------------------
    //   Edit page options
    // ------------------------------------
    public function createModel() {
        $instance = new Role();
        return $instance;
    }

    public function find($id)
    {
        return Role::find($id);
    }

    public function editForm($form, $model)
    {
        $selected = json_decode($model->permissions)?:[];

        $form
            ->text('name', 'Name', ['validation' => 'required'])
            ->multi_select('permissions', 'Permissions', Config::get('content.permissions'), $selected);
    }

    public static function addRoutes($name, $concrete_class) {
        $user_roles_controller = '\Reflexions\Content\Admin\Http\Controllers\UserRolesController';
        parent::addRoutes($name, $concrete_class);
        Route::get('/edit-all', [
            'as' => 'admin-' . $name . '-edit-all',
            'uses' => "$user_roles_controller@editAll"
        ]);
        Route::patch('/update-all', [
            'as' => 'admin-' . $name . '-update-all',
            'uses' => "$user_roles_controller@updateAll"
        ]);
    }
}