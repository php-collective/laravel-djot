<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Facades;

use Illuminate\Support\Facades\Facade;
use PhpCollective\LaravelDjot\Service\DjotManager;

/**
 * @method static string toHtml(string $djot, string $converter = 'default')
 * @method static string toHtmlRaw(string $djot)
 * @method static string toText(string $djot, string $converter = 'default')
 * @method static \PhpCollective\LaravelDjot\Service\DjotConverterInterface converter(string $name = 'default')
 * @method static array<string, \PhpCollective\LaravelDjot\Service\DjotConverterInterface> getConverters()
 *
 * @see \PhpCollective\LaravelDjot\Service\DjotManager
 */
class Djot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DjotManager::class;
    }
}
