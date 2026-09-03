<?php

declare(strict_types=1);

namespace Karnoweb\Translation;

use Illuminate\Support\Traits\Macroable;
use Karnoweb\Translation\Support\ResolvesConfiguredModels;

/**
 * Thin manager: config access, model resolution, and host macros.
 */
class Translation
{
    use Macroable;
    use ResolvesConfiguredModels;

    public function config(string $key, mixed $default = null): mixed
    {
        return config("translation.{$key}", $default);
    }
}
