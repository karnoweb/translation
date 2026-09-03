<?php

declare(strict_types=1);

namespace Karnoweb\Translation\Facades;

use Illuminate\Support\Facades\Facade;
use Karnoweb\Translation\Translation as TranslationManager;

/**
 * @method static mixed                                             config(string $key, mixed $default = null)
 * @method static class-string<\Illuminate\Database\Eloquent\Model> model(string $key)
 * @method static \Illuminate\Database\Eloquent\Model               newModel(string $key)
 *
 * @see TranslationManager
 */
final class Translation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translation';
    }
}
