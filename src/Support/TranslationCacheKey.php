<?php

declare(strict_types=1);

namespace Karnoweb\Translation\Support;

final class TranslationCacheKey
{
    public static function for(string $modelClass, int $modelId, string $key, string $locale): string
    {
        return sprintf(
            'translation:%s:%d:%s:%s',
            str_replace('\\', '_', $modelClass),
            $modelId,
            $key,
            $locale
        );
    }

    public static function forget(string $modelClass, int $modelId, string $key, string $locale): void
    {
        cache()->forget(self::for($modelClass, $modelId, $key, $locale));
    }

    /**
     * @param list<string> $keys
     */
    public static function forgetModel(string $modelClass, int $modelId, array $keys, ?string $locale = null): void
    {
        if ($keys === []) {
            return;
        }

        if ($locale !== null) {
            foreach ($keys as $key) {
                self::forget($modelClass, $modelId, $key, $locale);
            }

            return;
        }

        foreach (config('app.supported_locales', [config('app.locale', 'en')]) as $supportedLocale) {
            foreach ($keys as $key) {
                self::forget($modelClass, $modelId, $key, (string) $supportedLocale);
            }
        }
    }
}
