<?php

declare(strict_types=1);

namespace Squidweb\AdCenter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Squidweb\AdCenter\AdCenter context(string|array ...$contexts)
 * @method static list<string> contexts()
 * @method static bool enabled()
 * @method static string site()
 * @method static string scriptUrl()
 *
 * @see \Squidweb\AdCenter\AdCenter
 */
class AdCenter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Squidweb\AdCenter\AdCenter::class;
    }
}
