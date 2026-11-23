# AdCenter client voor Laravel

Laravel-package waarmee onze eigen sites advertenties van AdCenter tonen. Het
package zet het loader-script in de layout, plaatst advertentieslots in views
en geeft de context (het onderwerp) van een pagina door aan AdCenter.

- PHP 8.4+
- Laravel 12 of 13

## Installeren

Het package staat niet op Packagist, maar wel als openbare repository op GitHub:
<https://github.com/squid-web/laravel-adcenter-client>. Voeg die toe aan de
`composer.json` van de site:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/squid-web/laravel-adcenter-client"
    }
]
```

Daarna:

```bash
composer require squidweb/laravel-adcenter-client:^0.1
```

Tijdens het ontwikkelen aan het package zelf kan het ook via een
path-repository naar een lokale checkout:

```json
"repositories": [
    {
        "type": "path",
        "url": "../laravel-adcenter-client",
        "options": { "symlink": true }
    }
]
```

De ServiceProvider en de facade `AdCenter` worden automatisch geregistreerd
(package auto-discovery).

Wil je de config aanpassen, publiceer hem dan:

```bash
php artisan vendor:publish --tag=adcenter-config
```

## Configuratie (.env)

```dotenv
# Basis-URL van AdCenter (zonder /l.js). Standaard: https://adcenter.squid-web.nl
ADCENTER_URL=https://adcenter.squid-web.nl

# Sitecode van deze site in AdCenter. Zonder sitecode staat AdCenter uit.
ADCENTER_SITE=mijnsite

# Zet op false om AdCenter uit te schakelen (bijv. lokaal). Standaard: true
ADCENTER_ENABLED=true
```

AdCenter is alleen actief als `ADCENTER_ENABLED` waar is **én** `ADCENTER_SITE`
gevuld is. Staat AdCenter uit, dan rendert het script niets en tonen slots
alleen hun eigen fallback-inhoud (zonder wrapper-div). Een site zonder AdCenter
houdt zo gewoon zijn eigen inhoud.

## Gebruik

### Layout

Zet het script één keer in de `<head>` (of onderaan de `<body>`):

```blade
<head>
    ...
    <x-adcenter::script />
</head>
```

Dit rendert:

```html
<style data-adcenter>…</style>
<script async src="https://adcenter.squid-web.nl/l.js" data-site="mijnsite" data-ctx="costa-rica,natuur"></script>
```

De korte `<style>` ervoor zorgt dat de pagina klopt vanaf de eerste weergave, nog voordat het (async) script
draait: slots met `device="mobile"` of `device="desktop"` verschijnen alleen op dat scherm (mobiel is tot en met
767px breed), en een slot zonder `height` is onzichtbaar tot er een advertentie in staat. Zo breekt een leeg
slot (bijvoorbeeld een `col-12` tussen producten) geen rij of raster. Een `nonce` gaat ook naar de `<style>`.

`data-ctx` wordt weggelaten als er geen contexten zijn. Extra contexten kun je
ook vanuit de view meegeven, en extra attributen (zoals een CSP-nonce) worden
doorgegeven aan de script-tag:

```blade
<x-adcenter::script :context="['reizen', $category->name]" nonce="{{ Vite::cspNonce() }}" />
```

> Contexten die ná het renderen van het script worden toegevoegd, komen er niet
> meer in. Zet ze dus in de controller, of zet het script onderaan de body.

### Controller

Geef in de controller aan waar de pagina over gaat:

```php
use Squidweb\AdCenter\Facades\AdCenter;

public function show(Destination $destination)
{
    AdCenter::context($destination->slug);

    // Meerdere contexten mag ook, als losse argumenten of als array:
    AdCenter::context($destination->country->slug, ['reizen', 'natuur']);

    return view('destinations.show', compact('destination'));
}
```

Contexten worden genormaliseerd met `Str::slug` (`'Costa Rica'` wordt
`costa-rica`); lege waarden en dubbelen vallen weg, de volgorde blijft behouden
en er worden maximaal 10 contexten doorgestuurd.

De `AdCenter`-klasse is als *scoped* binding geregistreerd: elke request (ook
onder Octane) begint met een lege lijst contexten.

### View: slots

```blade
<x-adcenter::slot name="sidebar" :height="250" class="my-6" />
```

```html
<div data-adcenter-slot="sidebar" style="min-height:250px" class="my-6"></div>
```

- `name` is de slotnaam zoals die in AdCenter is ingesteld.
- `height` (optioneel, in pixels) reserveert ruimte, zodat de pagina niet
  verspringt als de advertentie laadt (voorkomt CLS). Zonder `height` is het
  slot onzichtbaar tot er een advertentie in staat.
- `device` (optioneel): `mobile` of `desktop`. Het slot verschijnt dan alleen
  op dat soort scherm; zonder `device` altijd. Zo kan hetzelfde slot op mobiel
  ergens anders staan dan op desktop.
- Extra attributen (`class`, `id`, `style`, …) worden doorgegeven aan de div.

**Fallback-inhoud**: wat je tussen de tags zet, wordt getoond als AdCenter
niets heeft voor dit slot:

```blade
<x-adcenter::slot name="sidebar" :height="250">
    @include('partials.newsletter-signup')
</x-adcenter::slot>
```

```html
<div data-adcenter-slot="sidebar" style="min-height:250px"><template data-adcenter-fallback>…nieuwsbrief…</template></div>
```

Staat AdCenter uit, dan wordt alleen de fallback-inhoud gerenderd, zonder div.

### In-article-advertenties

AdCenter kan automatisch advertenties tussen de alinea's van een artikel
plaatsen. Markeer daarvoor het element met de artikeltekst met het attribuut
`data-adcenter-article`:

```blade
<div class="prose" data-adcenter-article>
    {!! $article->body !!}
</div>
```

Of gebruik het component, dat een `<div data-adcenter-article>` om de inhoud zet:

```blade
<x-adcenter::article class="prose">
    {!! $article->body !!}
</x-adcenter::article>
```

## Testen

```bash
composer install
vendor/bin/phpunit
```
