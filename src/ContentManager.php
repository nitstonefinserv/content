<?php
namespace Reflexions\Content;

use InvalidArgumentException;
use \Route;
use \Reflexions\Content\Admin\Http\Controllers\ContentTraitAdminController;
use Illuminate\Database\Eloquent\Relations\Relation;
use \Reflexions\Content\Models\Content;

class ContentManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The migration classnames
     *
     * @var array
     */
    protected $migrations;

    /**
     * The laravel package name
     *
     * @var string
     */
    protected $package;

    /**
     * Create a new Content manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app, $migrations, $package)
    {
        $this->app = $app;
        $this->migrations = $migrations;
        $this->package = $package;
    }

    /**
     * Register a collection of admin-* routes based on the provided config classes
     *
     * @see \Reflexions\Content\Admin\AdminOptions
     * @see \Reflexions\Content\Admin\AdminListPage
     * @see \Reflexions\Content\Admin\AdminEditPage
     * @param string $name
     * @param string $config_collection Names of one or more config classes extending AdminOptionsInterface
     * @return void
     */
    public function admin($name, ...$config_collection)
    {
        Route::group(['prefix' => $name], function() use ($name, $config_collection) {
            foreach($config_collection as $config) {
                call_user_func($config.'::addRoutes', $name, $config);
            }
        });
    }

    /**
     * Register a collection of admin-* routes based on the provided config classes
     *
     * @see \Reflexions\Content\Admin\AdminOptions
     * @see \Reflexions\Content\Admin\AdminListPage
     * @see \Reflexions\Content\Admin\AdminEditPage
     * @param string $name
     * @param string $middleware
     * @param string $config_collection Names of one or more config classes extending AdminOptionsInterface
     * @return void
     */
    public function adminWithMiddleWare($name, $middleware, ...$config_collection)
    {
        Route::group(['prefix' => $name, 'middleware' => $middleware], function() use ($name, $config_collection) {
            foreach($config_collection as $config) {
                call_user_func($config.'::addRoutes', $name, $config);
            }
        });
    }

    // cache used by Content#elixir()
    private static $manifest = null;

    /**
     * Elixir helper for published package assets
     * @param string $file
     * @return string
     */
    public function elixir($file)
    {
        $dist_dir = 'vendor/'.ContentServiceProvider::NAME;
        
        if (is_null(self::$manifest)) {
            $manifestPath = public_path($dist_dir . '/rev-manifest.json');
            if (!file_exists($manifestPath)) {
                throw new InvalidArgumentException("Reflexions\\Content public vendor files not published.");
            }
            self::$manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (isset(self::$manifest[$file])) {
            return '/'.$dist_dir.'/'.self::$manifest[$file];
        }

        throw new InvalidArgumentException("File {$file} not defined in reflexions-content asset manifest.");
    }

    /**
     * Create tables associated with Content Traits and ContentTypes
     * ### destined to be replaced by published migrations
     * @return void
     */
    public function migrate($additional=[])
    {
        foreach($this->migrations as $class) {
            $migration = new $class;
            $migration->up();
        }
        foreach($additional as $class) {
            $migration = new $class;
            $migration->up();
        }
           
    }

    /**
     * Remove tables associated with Content Traits and ContentTypes
     * ### destined to be replaced by published migrations
     * @return void
     */
    public function rollback($additional=[])
    {
        foreach(array_reverse($this->migrations) as $class) {
            $migration = new $class;
            $migration->down();
        }
        foreach(array_reverse($additional) as $class) {
            $migration = new $class;
            $migration->down();
        }
    }

    /**
     * Return the laravel namespace for the package
     * @return string
     */
    public function package()
    {
        return $this->package;
    }

    /**
     * Add routes associated with Content package
     */
    public function addRoutes()
    {
        Route::group(['prefix' => 'content'], function() {
            Route::post(
                '/api-delete-previous-slug',
                [
                    'as' => 'content.api-delete-previous-slug',
                    'uses' => '\\'.ContentTraitAdminController::class.'@postApiDeletePreviousSlug',
                ]
            );
            Route::get(
                '/api-term-lookup/{group_slug}',
                [
                    'as' => 'content.api-term-lookup',
                    'uses' => '\\'.ContentTraitAdminController::class.'@getApiTermLookup',
                ]
            );
            Route::get(
                '/api-content-images-lookup/{content_id}',
                [
                    'as' => 'content.api-content-images-lookup',
                    'uses' => '\\'.ContentTraitAdminController::class.'@getApiContentImagesLookup',
                ]
            );
            Route::get(
                '/api-content-files-lookup/{content_id}',
                [
                    'as' => 'content.api-content-files-lookup',
                    'uses' => '\\'.ContentTraitAdminController::class.'@getApiContentFilesLookup',
                ]
            );
            Route::post(
                '/api-content-file-upload/{content_id}',
                [
                    'as' => 'content.api-content-file-upload',
                    'uses' => '\\'.ContentTraitAdminController::class.'@postApiContentFileUpload',
                ]
            );
            Route::post(
                '/api-content-file-delete/{content_id}',
                [
                    'as' => 'content.api-content-file-delete',
                    'uses' => '\\'.ContentTraitAdminController::class.'@postApiContentFileDelete',
                ]
            );
            Route::post(
                '/api-content-file-attribute-update/{content_id}',
                [
                    'as' => 'content.api-content-file-attribute-update',
                    'uses' => '\\'.ContentTraitAdminController::class.'@postApiContentFileAttributeUpdate',
                ]
            );
            Route::get(
                '/ckeditor-file-browser/{content_id}/{type}',
                [
                    'as' => 'content.ckeditor-file-browser',
                    'uses' => '\\'.ContentTraitAdminController::class.'@getCKEditorFileBrowser',
                ]
            );
        });
    }

    /**
     * morph the provided classname
     */
    public function morph($class)
    {
        return Content::morph($class);
    }
}
