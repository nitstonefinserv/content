<?php
namespace Reflexions\Content\Admin;

use Reflexions\Content\ContentServiceProvider;
use Reflexions\Content\Admin\Http\Controllers\AdminController;
use Route;
use URL;
use Config;
use Storage;

/**
 * Provides settings to AdminController
 */
abstract class AdminOptions extends AdminOptionsBase implements AdminOptionsInterface
{
    public $delete_files_from_storage = true;

    public static function getDiskName()
    {
        return Config::get('content.upload-disk');
    }

    public static function getDisk()
    {
        $diskPreference = static::getDiskName();
        return Storage::disk($diskPreference);
    }

    public static function getDiskConfigName()
    {
        $diskPreference = static::getDiskName();
        return "filesystems.disks.$diskPreference";
    }

    public static function getPathPrefix()
    {
        $diskConfigName = static::getDiskConfigName();

        return Config::get("$diskConfigName.root", '');
    }

    public static function getUrlPrefix()
    {
        $diskConfigName = static::getDiskConfigName();
        return Config::get("$diskConfigName.url");
    }

    public function __construct($name) {
        $this->name = $name;
    }
    
    /**
     * Returns the 'slug' associated with this resource.
     * @return string
     */
    public function name() { return $this->name; }


    /**
     * Label for model used in various places
     * @return string
     */
    public abstract function label();

    /**
     * Returns Eloquent QueryBuilder associated with report
     * @return QueryBuilder
     */
    abstract public function query();

    /**
     * Default pagesize for lists
     * @return int
     */
    public function pagesize() { return 15; }

    /**
     * Name of view to use to display list page
     * @return string
     */
    public function listViewName() { return ContentServiceProvider::NAME."::admin.list"; } 

    /**
     * Global actions from list page
     * @return array[Action]
     */
    public function listActions() {
        return [
            new Action('Export', 'fa-download', URL::route('admin-'.$this->name().'-export'))
        ];
    }

    /**
     * Create a fresh Model instance.
     * only created to support create action.
     * @return Model
     */
    public function createModel() {
        return null;
    }

    /**
     * View to use to display edit page
     * @return string
     */
    public function editViewName() {
        return ContentServiceProvider::NAME."::admin.edit";
    }
    
    /**
     * Default page icon (font awesome class)
     * @return string
     */
    public function pageIcon() {
        return "fa-table";
    }

    /**
     * Default DataTables order
     * @var array
     */
    public function order() { return [[0, "desc"]]; }

    /**
     * Should return list of ReportsTableColumn instances
     * @return array 
     */
    abstract public function tableColumns();

    /**
     * Returns Datatables instance.  Use https://github.com/yajra/laravel-datatables/tree/L4
     * @return Datatables
     */
    abstract public function datatables();


    /**
     * Called by Content::admin()
     * @param $name slug for routes
     * @param $concrete_class subclass defining particular admin configuration
     */
    public static function addRoutes($name, $concrete_class) {
        Route::get('/', ['as' => 'admin-'.$name.'-index', function() use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->index();
        }]);
        Route::get('/datatables', ['as' => 'admin-'.$name.'-datatables', function() use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->datatables();
        }]);
        Route::get('/export', ['as' => 'admin-'.$name.'-export', function() use ($name, $concrete_class) {
            return AdminController::factory($name, $concrete_class)->export();
        }]);

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
