<?php
namespace Reflexions\Content\Admin;

use Reflexions\Content\ContentServiceProvider;
use Reflexions\Content\Admin\Http\Controllers\AdminController;
use Route;
use URL;

/**
 * Provides settings to AdminController
 */
abstract class AdminOptionsBase
{
    public function __construct($name) {
        $this->name = $name;
    }
    
    /**
     * Returns the 'slug' associated with this resource.
     * @return string
     */
    public function name() { return $this->name; }

    /**
     * Returns an action object
     */
    public function action($label, $icon, $link, $type=null)
    {
        return new Action($label, $icon, $link, $type);
    }

    /**
     * Returns a table column object
     */
    public function column($field, $label, $options=[], $width='')
    {
        return new TableColumn($field, $label, $options, $width);
    }

    /**
     * Returns TableStatusColumn placeholder
     */
    public function statusColumn($label = 'Status')
    {
        return new TableStatusColumn($label);
    }
    
    /**
     * Returns TableActionColumn placeholder
     */
    public function actionColumn($label)
    {
        return new TableActionColumn($label);
    }

    /**
     * Return a configurable form instance
     */
    public function form($callback) {
        return new Form($callback);
    }
}