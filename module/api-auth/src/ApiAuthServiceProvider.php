<?php
namespace Cortexitsolution\ApiAuth;

use Cortexitsolution\ApiAuth\Events\UserLoginEvent;
use Cortexitsolution\ApiAuth\Listeners\UserLoginListener;
use Illuminate\Support\ServiceProvider;

class ApiAuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/lang','api-auth');

        $this->registerEvents();
    }
    public function register()
    {

    }
    protected function registerEvents()
    {
        $events = $this->app['events'];

        $events->listen(UserLoginEvent::class, UserLoginListener::class);

        $this->commands([
            \Cortexitsolution\ApiAuth\Commands\CleanOtps::class,
        ]);
    }
}

?>