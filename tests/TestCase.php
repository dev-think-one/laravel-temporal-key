<?php

namespace TemporalKey\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\Database\MigrateProcessor;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected function getPackageProviders($app)
    {
        return [
            \TemporalKey\ServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();

        $migrator = new MigrateProcessor($this, [
            '--path'     => __DIR__.'/Fixtures/migrations',
            '--realpath' => true,
        ]);
        $migrator->up();
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // $app['config']->set('temporal-key.some_key', 'some_value');
    }
}
