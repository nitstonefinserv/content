<?php namespace Reflexions\Content\Tests;

use Reflexions\Content\ContentServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('filesystems.default', 'testing');
        $app['config']->set('filesystems.disks.testing.driver', 'testing');
        \Content::addRoutes();
    }

    /**
     * Load Reflexions Content
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ContentServiceProvider::class,
            TestingFilesystemServiceProvider::class,
        ];
    }

    /**
     * Install migrations
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/../migrations'),
        ]);
    }

    /**
     * Rollback migrations
     *
     * @return void
     */
    public function tearDown() {
        $this->artisan('migrate:rollback');
        parent::tearDown();
    }
}