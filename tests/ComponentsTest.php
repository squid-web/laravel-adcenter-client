<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\Tests;

use PHPUnit\Framework\Attributes\Test;
use Squidweb\AdCenter\Facades\AdCenter;

class ComponentsTest extends TestCase
{
    private function disable(): void
    {
        config(['adcenter.enabled' => false]);
        $this->app->forgetScopedInstances();
    }

    #[Test]
    public function script_zonder_contexten(): void
    {
        $this->assertSame(
            '<script async src="https://ads.example.test/l.js" data-site="demo"></script>',
            $this->render('<x-adcenter::script />'),
        );
    }

    #[Test]
    public function script_met_contexten(): void
    {
        AdCenter::context('Costa Rica', 'Natuur');

        $this->assertSame(
            '<script async src="https://ads.example.test/l.js" data-site="demo" data-ctx="costa-rica,natuur"></script>',
            $this->render('<x-adcenter::script />'),
        );
    }

    #[Test]
    public function script_context_prop_voegt_contexten_toe(): void
    {
        AdCenter::context('costa-rica');

        $this->assertSame(
            '<script async src="https://ads.example.test/l.js" data-site="demo" data-ctx="costa-rica,reizen,natuur"></script>',
            $this->render('<x-adcenter::script :context="$extra" />', ['extra' => ['Reizen', 'Natuur']]),
        );
    }

    #[Test]
    public function script_geeft_extra_attributen_door(): void
    {
        $this->assertSame(
            '<script async src="https://ads.example.test/l.js" data-site="demo" nonce="abc123"></script>',
            $this->render('<x-adcenter::script nonce="abc123" />'),
        );
    }

    #[Test]
    public function script_escapet_waarden(): void
    {
        config(['adcenter.site' => 'a"b<c>', 'adcenter.url' => 'https://x.test/?a=1&b=2']);
        $this->app->forgetScopedInstances();

        $this->assertSame(
            '<script async src="https://x.test/?a=1&amp;b=2/l.js" data-site="a&quot;b&lt;c&gt;"></script>',
            $this->render('<x-adcenter::script />'),
        );
    }

    #[Test]
    public function script_rendert_niets_als_uitgeschakeld(): void
    {
        $this->disable();

        $this->assertSame('', $this->render('<x-adcenter::script />'));
    }

    #[Test]
    public function script_rendert_niets_zonder_sitecode(): void
    {
        config(['adcenter.site' => null]);
        $this->app->forgetScopedInstances();

        $this->assertSame('', $this->render('<x-adcenter::script />'));
    }

    #[Test]
    public function slot_zonder_hoogte(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="sidebar"></div>',
            $this->render('<x-adcenter::slot name="sidebar" />'),
        );
    }

    #[Test]
    public function slot_met_hoogte(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="sidebar" style="min-height:250px"></div>',
            $this->render('<x-adcenter::slot name="sidebar" :height="250" />'),
        );
    }

    #[Test]
    public function slot_met_hoogte_als_string_en_ongeldige_hoogte(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="top" style="min-height:90px"></div>',
            $this->render('<x-adcenter::slot name="top" height="90" />'),
        );

        $this->assertSame(
            '<div data-adcenter-slot="top"></div>',
            $this->render('<x-adcenter::slot name="top" height="0" />'),
        );
    }

    #[Test]
    public function slot_met_extra_class_en_attributen(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="sidebar" style="min-height:250px" class="my-4 mx-auto" id="ad-sidebar"></div>',
            $this->render('<x-adcenter::slot name="sidebar" :height="250" class="my-4 mx-auto" id="ad-sidebar" />'),
        );
    }

    #[Test]
    public function slot_vult_eigen_style_aan(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="sidebar" style="min-height:250px; width:300px;"></div>',
            $this->render('<x-adcenter::slot name="sidebar" :height="250" style="width:300px" />'),
        );
    }

    #[Test]
    public function slot_met_fallback_template(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="sidebar" style="min-height:250px"><template data-adcenter-fallback><a href="/nieuwsbrief">Nieuwsbrief</a></template></div>',
            $this->render('<x-adcenter::slot name="sidebar" :height="250"><a href="/nieuwsbrief">Nieuwsbrief</a></x-adcenter::slot>'),
        );
    }

    #[Test]
    public function slot_escapet_naam_en_attributen(): void
    {
        $this->assertSame(
            '<div data-adcenter-slot="a&quot;&gt;&lt;b" class="&quot;x&quot;"></div>',
            $this->render('<x-adcenter::slot :name="$name" :class="$class" />', ['name' => 'a"><b', 'class' => '"x"']),
        );
    }

    #[Test]
    public function uitgeschakeld_slot_toont_alleen_fallback(): void
    {
        $this->disable();

        $this->assertSame(
            '<a href="/nieuwsbrief">Nieuwsbrief</a>',
            $this->render('<x-adcenter::slot name="sidebar" :height="250" class="my-4"><a href="/nieuwsbrief">Nieuwsbrief</a></x-adcenter::slot>'),
        );
    }

    #[Test]
    public function uitgeschakeld_slot_zonder_fallback_rendert_niets(): void
    {
        $this->disable();

        $this->assertSame('', $this->render('<x-adcenter::slot name="sidebar" :height="250" />'));
    }

    #[Test]
    public function article_component_zet_attribuut_om_de_inhoud(): void
    {
        $this->assertSame(
            '<div data-adcenter-article><p>Tekst</p></div>',
            $this->render('<x-adcenter::article><p>Tekst</p></x-adcenter::article>'),
        );

        $this->assertSame(
            '<div data-adcenter-article class="prose"><p>Tekst</p></div>',
            $this->render('<x-adcenter::article class="prose"><p>Tekst</p></x-adcenter::article>'),
        );
    }
}
