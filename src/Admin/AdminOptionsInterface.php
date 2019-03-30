<?php
namespace Reflexions\Content\Admin;

/**
 * Provides settings to AdminController
 */
interface AdminOptionsInterface {
    /**
     * @param $name slug for routes
     */
    public function __construct($name);

    /**
     * @param $name slug for routes
     * @param $class implentation defining options for routes
     */
    public static function addRoutes($name, $class);
}