<?php

namespace TemporalKey;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/temporal-key.php' => config_path('temporal-key.php'),
            ], 'config');

            $this->commands([
                \TemporalKey\Console\Commands\PruneTemporalKeysCommand::class,
            ]);

            $this->registerMigrations();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/temporal-key.php', 'temporal-key');
    }

    protected function registerMigrations()
    {
        if (TemporalKey::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
