<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * <x-adcenter::article>…tekst…</x-adcenter::article>
 *
 * Zet een <div data-adcenter-article> om de artikeltekst, zodat AdCenter
 * daarin automatisch in-article-slots kan plaatsen.
 */
class Article extends Component
{
    public function render(): View
    {
        return view('adcenter::components.article');
    }
}
