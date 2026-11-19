<?php

declare(strict_types=1);

namespace Squidweb\AdCenter;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Houdt per request de AdCenter-instellingen en de contexten (onderwerpen) van
 * de huidige pagina bij. Wordt als scoped binding geregistreerd, zodat de
 * contexten onder Octane niet naar een volgend request lekken.
 */
class AdCenter
{
    /**
     * Maximaal aantal contexten dat naar AdCenter wordt gestuurd.
     */
    public const int MAX_CONTEXTS = 10;

    /**
     * @var list<string>
     */
    private array $contexts = [];

    public function __construct(
        private readonly string $url,
        private readonly string $site,
        private readonly bool $enabled = true,
    ) {}

    /**
     * Voeg een of meer contexten toe aan de huidige pagina.
     *
     * Waarden worden genormaliseerd met Str::slug; lege waarden en dubbelen
     * vallen weg, de volgorde blijft behouden en er worden er maximaal
     * MAX_CONTEXTS bewaard.
     *
     * @param  string|array<int, mixed>  ...$contexts
     */
    public function context(string|array ...$contexts): static
    {
        foreach (Arr::flatten($contexts) as $context) {
            if (count($this->contexts) >= self::MAX_CONTEXTS) {
                break;
            }

            if (! is_scalar($context)) {
                continue;
            }

            $slug = Str::slug((string) $context);

            if ($slug !== '' && ! in_array($slug, $this->contexts, true)) {
                $this->contexts[] = $slug;
            }
        }

        return $this;
    }

    /**
     * De genormaliseerde contexten van de huidige pagina.
     *
     * @return list<string>
     */
    public function contexts(): array
    {
        return $this->contexts;
    }

    /**
     * Is AdCenter ingeschakeld én is er een sitecode ingesteld?
     */
    public function enabled(): bool
    {
        return $this->enabled && trim($this->site) !== '';
    }

    /**
     * De sitecode zoals die in AdCenter bekend is.
     */
    public function site(): string
    {
        return trim($this->site);
    }

    /**
     * Volledige URL van het loader-script.
     */
    public function scriptUrl(): string
    {
        return rtrim(trim($this->url), '/').'/l.js';
    }
}
