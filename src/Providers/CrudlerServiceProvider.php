<?php

namespace LaravelCrudler\Providers;

use Illuminate\Support\ServiceProvider;

class CrudlerServiceProvider extends ServiceProvider
{
    public function register()
    {
        // retgistration config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/crudler.php',
            'crudler'
        );
    }

    public function boot()
    {
        // publish config
        $this->publishes([
            __DIR__.'/../../config/crudler.php' => config_path('crudler.php'),
        ], 'crudler-config');
    }
}