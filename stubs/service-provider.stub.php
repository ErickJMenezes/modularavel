<?= "<?php\n" ?>

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
<?php if (getenv('WITH_ROUTES')): ?>
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
<?php endif; if (getenv('WITH_VIEWS')): ?>
        $this->loadViewsFrom(__DIR__.'/../../resources/views', '<?= getenv('LIB_NAME') ?>');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/views/lang', '<?= getenv('LIB_NAME') ?>');
        Blade::componentNamespace('Libs\\<?= getenv('STUDLY_LIB_NAME') ?>\\Components', '{{LIB_NAME}}');
<?php endif; if (getenv('WITH_CONFIG')): ?>
        $this->mergeConfigFrom(
            __DIR__.'/../../config/<?= getenv('LIB_NAME') ?>.php', '<?= getenv('LIB_NAME') ?>'
        );
<?php endif; if(getenv('WITH_MIGRATIONS')): ?>
        $this->loadMigrationsFrom(
            __DIR__.'/../../database/migrations'
        );
<?php endif; ?>
    }
}
