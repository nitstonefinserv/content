<?php
namespace Reflexions\Content\Models;

class Status {

    const STUB = 'new';
    const DRAFT = 'draft';
    const SCHEDULED = 'scheduled';
    const PUBLISHED = 'published';
    const ARCHIVED = 'archived';

    /**
     * Get associative array of statuses for select list
     * @return array
     */
    public static function get($status = '')
    {
        $statuses = static::all();
        return !empty($statuses[$status]) ? $statuses[$status] : false;
    }

    /**
     * Get associative array of all statuses (including scheduled) for select list
     * @return array
     */
    public static function all()
    {
        return [
            static::STUB => ucfirst(static::STUB),
            static::DRAFT => ucfirst(static::DRAFT),
            static::SCHEDULED => ucfirst(static::SCHEDULED),
            static::PUBLISHED => ucfirst(static::PUBLISHED),
            static::ARCHIVED => ucfirst(static::ARCHIVED)
        ];
    }

    /**
     * Get associative array of statuses for select list
     * @return array
     */
    public static function toArray()
    {
        return [
            static::DRAFT => ucfirst(static::DRAFT),
            static::PUBLISHED => ucfirst(static::PUBLISHED),
            static::SCHEDULED => ucfirst(static::SCHEDULED),
            static::ARCHIVED => ucfirst(static::ARCHIVED)
        ];
    }

    /**
     * Get comma separated string of statuses
     * @return string
     */
    public static function toString()
    {
        return join(',', array_keys(static::toArray()));
    }


    /**
     * Get label associated with status
     * @return array
     */
    public static function label($status)
    {
        $labels = static::all();
        return empty($labels[$status]) ? false : $labels[$status];
    }

    /**
     * Get array of backgrounds associated with statuses
     * @return array
     */
    public static function getBackgrounds()
    {
        return [
            static::DRAFT => 'warning',
            static::PUBLISHED => 'success',
            static::SCHEDULED => 'info',
            static::ARCHIVED => '',
        ];
    }

    /**
     * Get one status background by name
     * @param  string $status
     * @return string
     */
    public static function background($status)
    {
        $backgrounds = static::getBackgrounds();
        return !empty($backgrounds[$status]) ? $backgrounds[$status] : false;
    }

}