<?php

namespace ErickJMenezes\Modularavel\Providers;

use ErickJMenezes\Modularavel\Commands\MakeLib;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
use ErickJMenezes\Modularavel\UseCases\GenerateLibrary;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;

class ModularavelServiceProvider extends ServiceProvider
{
    private const MODULARAVEL_CONFIG_PATH = __DIR__.'/../../config/modularavel.php';

    public function register()
    {
        $this->mergeConfigFrom(
            self::MODULARAVEL_CONFIG_PATH, 'modularavel'
        );

        $this->app->bind(Generator::class);

        $this->app->bind(GenerateLibrary::class, function (Application $app) {
            return new GenerateLibrary(
                $app->make(Generator::class),
                $app->make(Composer::class),
                config('modularavel.libraries_directory'),
                base_path(),
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            self::MODULARAVEL_CONFIG_PATH => config_path('modularavel.php'),
        ]);
        $this->commands([
            MakeLib::class,
        ]);
    }
}
