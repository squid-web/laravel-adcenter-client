<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Squidweb\AdCenter\AdCenter;

/**
 * <x-adcenter::script :context="$extra" />
 *
 * Rendert het AdCenter-loader-script. Rendert niets als AdCenter uit staat.
 * Extra attributen (bijv. een CSP-nonce) worden aan de script-tag toegevoegd.
 *
 * Ervoor staat een paar regels CSS, zodat de pagina vanaf de eerste weergave klopt, nog voordat het (async)
 * script draait: slots met device="mobile" of "desktop" alleen op dat scherm, en een slot zonder
 * gereserveerde hoogte onzichtbaar tot er iets in staat (zo breekt een lege col-12 geen rij producten).
 */
class Script extends Component
{
    /**
     * Dezelfde regels als in het loader-script van AdCenter; mobiel is tot en met 767px breed.
     */
    public const STYLES = '@media (max-width:767px){[data-adcenter-device=desktop],[data-adcenter-slot][device=desktop]{display:none!important}}'
        .'@media (min-width:768px){[data-adcenter-device=mobile],[data-adcenter-slot][device=mobile]{display:none!important}}'
        .'[data-adcenter-slot]:empty:not([style*="min-height"]){display:none}'
        .'[data-adcenter-slot]:not([style*="min-height"]):not(:has(> :not(template))){display:none}';

    /**
     * @param  string|array<int, mixed>|null  $context  Extra contexten voor deze pagina.
     */
    public function __construct(
        private readonly AdCenter $adCenter,
        string|array|null $context = null,
    ) {
        if ($context !== null) {
            $this->adCenter->context($context);
        }
    }

    public function shouldRender(): bool
    {
        return $this->adCenter->enabled();
    }

    public function render(): View
    {
        return view('adcenter::components.script', [
            'src' => $this->adCenter->scriptUrl(),
            'site' => $this->adCenter->site(),
            'ctx' => implode(',', $this->adCenter->contexts()),
            'styles' => self::STYLES,
        ]);
    }
}
