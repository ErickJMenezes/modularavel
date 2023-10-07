<?php

namespace ErickJMenezes\Modularavel\Providers;

use ErickJMenezes\Modularavel\Commands\InstallCommand;
use ErickJMenezes\Modularavel\Commands\ModuleMakeLibCommand;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
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
    }

    public function boot()
    {
        $this->publishes([
            self::MODULARAVEL_CONFIG_PATH => config_path('modularavel.php'),
        ]);
        $this->commands([
            InstallCommand::class,
            ModuleMakeLibCommand::class,
        ]);
    }
}
