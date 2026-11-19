<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Squidweb\AdCenter\AdCenterServiceProvider;
use Squidweb\AdCenter\Facades\AdCenter;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [AdCenterServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['AdCenter' => AdCenter::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('adcenter.url', 'https://ads.example.test/');
        $app['config']->set('adcenter.site', 'demo');
        $app['config']->set('adcenter.enabled', true);
    }

    protected function render(string $template, array $data = []): string
    {
        return trim((string) $this->blade($template, $data));
    }
}
