<?php

namespace Codemen\FakeField;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FakeFieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('fakefield.php'),
            ], 'config');
        }

        Blade::directive('fakeKey', function () {
            return '<?php echo \'<input type="hidden" value="\'.fake_key().\'" name="_fake_key">\'  ?>';
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'fakefield');

        // Register the main class to use with the facade
        $this->app->singleton('FakeField', function () {
            return new Main;
        });


    }

}
