<?php
namespace Reflexions\Content\Admin;

use Reflexions\Content\ContentServiceProvider;
use Reflexions\Content\Admin\Http\Controllers\AdminController;
use Route;
use URL;

/**
 * Provides settings to AdminController
 */
abstract class AdminEditPage implements AdminOptionsInterface
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
     * Create a fresh Model instance.
     * only created to support create action.
     * @return Model
     */
    public function createModel() {
        return null;
    }
    
    /**
     * Default page icon (font awesome class)
     * @return string
     */
    public function pageIcon() {
        return "fa-table";
    }

    /**
     * Called by Content::admin()
     * @param $name slug for routes
     * @param $concrete_class subclass defining particular admin configuration
     */
    public static function addRoutes($name, $concrete_class) {
        Route::get('/create', ['as' => 'admin-'.$name.'-create', function() use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->create();
        }]);
        Route::post('/store', ['as' => 'admin-'.$name.'-store', function() use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->store();
        }]);

        Route::get('/edit/{id}', ['as' => 'admin-'.$name.'-edit', function($id) use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->edit($id);
        }]);
        Route::patch('/update/{id}', ['as' => 'admin-'.$name.'-update', function($id) use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->update($id);
        }]);

        Route::delete('/delete/{id}', ['as' => 'admin-'.$name.'-delete', function($id) use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->delete($id);
        }]);
    }
}