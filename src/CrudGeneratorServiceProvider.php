<?php

namespace Thickas\CrudGenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/crud-generator/'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Thickas\CrudGenerator\Commands\CrudCommand',
            'Thickas\CrudGenerator\Commands\CrudControllerCommand',
            'Thickas\CrudGenerator\Commands\CrudModelCommand',
            'Thickas\CrudGenerator\Commands\CrudMigrationCommand'
        );
    }
}
