<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Squidweb\AdCenter\AdCenter;

/**
 * <x-adcenter::slot name="sidebar" :height="250">fallback</x-adcenter::slot>
 *
 * Rendert een advertentieplek. De slot-inhoud dient als fallback: AdCenter
 * toont die als er niets is voor dit slot. Staat AdCenter uit, dan wordt
 * alleen de fallback-inhoud gerenderd, zonder wrapper-div.
 */
class Slot extends Component
{
    public readonly ?int $height;

    public function __construct(
        private readonly AdCenter $adCenter,
        public readonly string $name,
        int|string|null $height = null,
    ) {
        $height = filter_var($height, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        $this->height = $height === false ? null : $height;
    }

    /**
     * Standaardattributen van de wrapper-div; extra attributen van de
     * gebruiker worden hiermee samengevoegd (class en style worden aangevuld).
     *
     * @return array<string, string>
     */
    public function wrapperAttributes(): array
    {
        $attributes = ['data-adcenter-slot' => $this->name];

        if ($this->height !== null) {
            $attributes['style'] = "min-height:{$this->height}px";
        }

        return $attributes;
    }

    public function render(): View
    {
        return view('adcenter::components.slot', [
            'enabled' => $this->adCenter->enabled(),
        ]);
    }
}
