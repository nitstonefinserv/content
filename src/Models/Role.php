<?php namespace Reflexions\Content\Models;

class Role extends \Eloquent
{
    const SUPER_ADMIN = 1;

    protected $fillable = ['name', 'permissions'];

    public function scopeWithoutSuperAdmin($query)
    {
        return $query->where('id', '!=', static::SUPER_ADMIN);
    }
}
