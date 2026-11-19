<?php

declare(strict_types=1);

namespace Squidweb\AdCenter;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AdCenterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/adcenter.php', 'adcenter');

        // Scoped (niet singleton): onder Octane krijgt elk request een
        // nieuwe instantie, zodat contexten niet tussen requests lekken.
        $this->app->scoped(AdCenter::class, function (Application $app): AdCenter {
            $config = $app->make('config');

            return new AdCenter(
                url: (string) $config->get('adcenter.url', ''),
                site: (string) $config->get('adcenter.site', ''),
                enabled: filter_var($config->get('adcenter.enabled', true), FILTER_VALIDATE_BOOL),
            );
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'adcenter');

        Blade::componentNamespace('Squidweb\\AdCenter\\View\\Components', 'adcenter');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/adcenter.php' => config_path('adcenter.php'),
            ], 'adcenter-config');
        }
    }
}
