<?php
namespace Reflexions\Content;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Facades\Image as ImageFacade;
use Intervention\Image\ImageServiceProvider;
use Yajra\Datatables\DatatablesServiceProvider;

class ContentServiceProvider extends ServiceProvider
{
    const NAME = 'content';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(static::configFile(), self::NAME);
        $this->app->singleton(self::NAME, function ($app) {
            return new ContentManager($app, [
                Migrations\ContentMigration::class,
                Migrations\SlugsMigration::class,
                Migrations\TagsMigration::class,
                Migrations\FilesMigration::class,
            ],
            self::NAME);
        });

        // dependencies
        $this->app->register(DatatablesServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);

        // Register Content Facade
        $loader = AliasLoader::getInstance();
        $loader->alias('Content', ContentFacade::class);
        $loader->alias('Form', FormFacade::class);
        $loader->alias('HTML', HtmlFacade::class);
        $loader->alias('Image', ImageFacade::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            self::NAME,
        ];
    }

    /**
     * Package boot hook for Laravel
     * 
     */
    public function boot()
    {
        // load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', self::NAME);
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', self::NAME);

        // Publish a config file
        $this->publishes(
            [ static::configFile() => config_path(basename(static::configFile())) ],
            'config'
        );
        $this->publishes(
            [ __DIR__.'/../dist' => public_path('vendor/'.self::NAME) ],
            'public'
        );

        $morphMap = Relation::morphMap();
        $morphMap['content'] = Models\Content::class;
        $morphMap['content-file'] = Models\File::class;
        $morphMap['content-slug'] = Models\Slug::class;
        $morphMap['content-status'] = Models\Status::class;
        $morphMap['content-tag'] = Models\Tag::class;
        Relation::morphMap($morphMap);
    }

    private static function configFile() {
        return __DIR__.'/../config/'.self::NAME.'.php';
    }

}
