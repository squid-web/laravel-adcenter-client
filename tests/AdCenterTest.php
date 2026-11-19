<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\Tests;

use PHPUnit\Framework\Attributes\Test;
use Squidweb\AdCenter\AdCenter;
use Squidweb\AdCenter\Facades\AdCenter as AdCenterFacade;

class AdCenterTest extends TestCase
{
    #[Test]
    public function contexten_worden_genormaliseerd_ontdubbeld_en_in_volgorde_bewaard(): void
    {
        $adCenter = new AdCenter('https://ads.example.test', 'demo');

        $adCenter
            ->context('Costa Rica', '', '  ')
            ->context(['Ångström Café', ['costa-rica', 'Wandelen & Fietsen']])
            ->context('COSTA RICA');

        $this->assertSame(['costa-rica', 'angstrom-cafe', 'wandelen-fietsen'], $adCenter->contexts());
    }

    #[Test]
    public function er_worden_maximaal_tien_contexten_bewaard(): void
    {
        $adCenter = new AdCenter('https://ads.example.test', 'demo');

        $adCenter->context(array_map(fn (int $i): string => "ctx {$i}", range(1, 15)));

        $this->assertCount(AdCenter::MAX_CONTEXTS, $adCenter->contexts());
        $this->assertSame('ctx-1', $adCenter->contexts()[0]);
        $this->assertSame('ctx-10', $adCenter->contexts()[9]);
    }

    #[Test]
    public function enabled_vereist_een_sitecode(): void
    {
        $this->assertTrue((new AdCenter('https://x.test', 'demo'))->enabled());
        $this->assertFalse((new AdCenter('https://x.test', 'demo', false))->enabled());
        $this->assertFalse((new AdCenter('https://x.test', ''))->enabled());
        $this->assertFalse((new AdCenter('https://x.test', '   '))->enabled());
    }

    #[Test]
    public function script_url_heeft_geen_dubbele_slash(): void
    {
        $this->assertSame('https://x.test/l.js', (new AdCenter('https://x.test/', 'demo'))->scriptUrl());
        $this->assertSame('https://x.test/l.js', (new AdCenter('https://x.test', 'demo'))->scriptUrl());
    }

    #[Test]
    public function de_binding_is_scoped_en_de_facade_werkt(): void
    {
        AdCenterFacade::context('Bali');

        $this->assertSame(['bali'], app(AdCenter::class)->contexts());
        $this->assertSame(app(AdCenter::class), app(AdCenter::class));

        // Na het resetten van scoped instanties (zoals Octane per request doet)
        // begint een nieuwe instantie zonder contexten.
        $this->app->forgetScopedInstances();

        $this->assertSame([], app(AdCenter::class)->contexts());
    }

    #[Test]
    public function config_waarden_worden_gebruikt(): void
    {
        $this->assertTrue(AdCenterFacade::enabled());
        $this->assertSame('demo', AdCenterFacade::site());
        $this->assertSame('https://ads.example.test/l.js', AdCenterFacade::scriptUrl());
    }

    #[Test]
    public function enabled_als_string_uit_env_wordt_correct_gelezen(): void
    {
        config(['adcenter.enabled' => 'false']);
        $this->app->forgetScopedInstances();

        $this->assertFalse(AdCenterFacade::enabled());
    }
}
