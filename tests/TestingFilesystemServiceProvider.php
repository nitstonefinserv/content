<?php namespace Reflexions\Content\Tests;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Storage;


class TestingFilesystemServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('testing', function($app, $config) {
            return new Filesystem(new MemoryAdapter());
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}