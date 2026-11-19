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
 */
class Script extends Component
{
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
        ]);
    }
}
