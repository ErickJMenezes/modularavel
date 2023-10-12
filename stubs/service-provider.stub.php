<?= '<?php' ?>

namespace Libs\<?= getenv('STUDLY_LIB_NAME') ?>\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class <?= getenv('STUDLY_LIB_NAME') ?>ServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', '<?= getenv('LIB_NAME') ?>');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/views/lang', '<?= getenv('LIB_NAME') ?>');
        Blade::componentNamespace('Libs\\<?= getenv('STUDLY_LIB_NAME') ?>\\Components', '{{LIB_NAME}}');
    }
}
