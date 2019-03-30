<?php namespace Reflexions\Content\Traits;

use Illuminate\Support\Facades\Request;
use View;
use Content;
use Reflexions\Content\Admin\Form\Field;
use Reflexions\Content\Models\Role;

trait UserRolesTrait
{
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function setRolesAttribute($value)
    {
        if (!$this->id) return;

        $this->roles()->detach();

        if ($value) {
            $this->roles()->attach($value);
        }
    }

    public function isSuperAdmin()
    {
        return in_array(Role::SUPER_ADMIN, $this->roles->pluck('id')->toArray());
    }

    public function hasRoleWithId($id)
    {
        if ($this->isSuperAdmin()) return true;

        return in_array($id, $this->roles->pluck('id')->toArray());
    }

    public function hasRole($value)
    {
        return in_array(
            strtolower($value),
            $this->roles
                ->pluck('name')
                ->map(function ($role) {
                    return strtolower($role);
                })
                ->toArray()
        );
    }

    public function hasPermission($value)
    {
        if ($this->isSuperAdmin()) return true;

        return $this->roles
            ->map(function ($role) {
                return json_decode($role->permissions) ?: [];
            })
            ->filter(function ($permission) use ($value) {
                return in_array($value, $permission);
            })
            ->isNotEmpty();
    }

    public function getUserRoleField($options = [])
    {
        $attribute = 'roles';
        $values = Role::pluck('name', 'id');
        if (!$this->isSuperAdmin()) unset($values[Role::SUPER_ADMIN]);
        $selected = $this->roles->pluck('id')->toArray();
        $label = 'Role';

        if (!Request::has($attribute)) Request::merge([$attribute => []]);
        return new Field(
            $attribute,
            isset($options['validation']) ? $options['validation'] : '',
            View::make(
                Content::package() . '::admin.components.multi-select',
                compact('attribute', 'values', 'selected', 'label', 'options')
            ),
            function ($value) {
                return $value;
            },
            // save handler
            function ($field_name, $model) use ($attribute) {
                if ($field_name == $attribute) {
                    $model->roles = Request::input($attribute);
                }
            }
        );
    }
}
