<?php

declare(strict_types=1);

namespace Karnoweb\Translation\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Karnoweb\Translation\Support\TranslationCacheKey;

trait HasTranslation
{
    /**
     * Translations for the current app locale.
     */
    public function translations(): MorphMany
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $translationClass */
        $translationClass = config('translation.models.translation');

        return $this->morphMany($translationClass, 'translatable')
            ->where('locale', app()->getLocale());
    }

    /**
     * All locales (no locale constraint) — use for admin multi-lang writes / fallback reads.
     */
    public function translationsPure(): MorphMany
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $translationClass */
        $translationClass = config('translation.models.translation');

        return $this->morphMany($translationClass, 'translatable');
    }

    protected function scopeSearch(Builder $query, $keyword): Builder
    {
        return $query->whereHas('translationsPure', function ($query) use ($keyword): void {
            $query->where('value', 'like', '%' . $keyword . '%');
        });
    }

    public function getAttribute($key): mixed
    {
        if (! $key) {
            return null;
        }

        if (array_key_exists($key, $this->attributes) ||
            array_key_exists($key, $this->casts) ||
            $this->hasGetMutator($key) ||
            $this->hasAttributeMutator($key) ||
            $this->isClassCastable($key)) {
            return $this->getAttributeValue($key);
        }

        if (in_array($key, $this->translatable ?? [], true)) {
            return $this->resolveTranslatedAttribute($key);
        }

        return $this->isRelation($key) || $this->relationLoaded($key)
            ? $this->getRelationValue($key)
            : $this->throwMissingAttributeExceptionIfApplicable($key);
    }

    public function setTranslation(string $key, string $value, ?string $locale = null): static
    {
        $locale ??= app()->getLocale();
        $modelId = (int) Arr::get($this->attributes, 'id', 0);

        if ($modelId === 0) {
            return $this;
        }

        $this->translationsPure()->updateOrCreate([
            'key' => $key,
            'locale' => $locale,
        ], [
            'value' => $value,
        ]);

        TranslationCacheKey::forget(static::class, $modelId, $key, $locale);

        if ($this->relationLoaded('translationsPure')) {
            $this->unsetRelation('translationsPure');
        }

        if ($this->relationLoaded('translations')) {
            $this->unsetRelation('translations');
        }

        return $this;
    }

    public function forgetTranslationCache(?string $key = null, ?string $locale = null): static
    {
        $modelId = (int) Arr::get($this->attributes, 'id', 0);

        if ($modelId === 0) {
            return $this;
        }

        $keys = $key !== null ? [$key] : ($this->translatable ?? []);

        TranslationCacheKey::forgetModel(static::class, $modelId, $keys, $locale);

        return $this;
    }

    protected function resolveTranslatedAttribute(string $key): mixed
    {
        $locale = app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'en');
        $modelId = (int) Arr::get($this->attributes, 'id', 0);

        if ($modelId === 0) {
            return null;
        }

        $value = $this->resolveFromLoadedTranslations($key, $locale);

        if ($value !== null) {
            return $value;
        }

        $value = $this->rememberTranslation(
            static::class,
            $modelId,
            $key,
            $locale,
            fn () => $this->translationsPure()
                ->where('key', $key)
                ->where('locale', $locale)
                ->value('value')
        );

        if (($value === null || $value === '') && $fallback !== $locale) {
            $fallbackValue = $this->resolveFromLoadedTranslations($key, $fallback);

            if ($fallbackValue !== null) {
                return $fallbackValue;
            }

            return $this->rememberTranslation(
                static::class,
                $modelId,
                $key,
                $fallback,
                fn () => $this->translationsPure()
                    ->where('key', $key)
                    ->where('locale', $fallback)
                    ->value('value')
            );
        }

        return $value;
    }

    protected function resolveFromLoadedTranslations(string $key, string $locale): mixed
    {
        foreach (['translationsPure', 'translations'] as $relation) {
            if (! $this->relationLoaded($relation)) {
                continue;
            }

            $match = $this->getRelation($relation)
                ->first(fn ($translation): bool => $translation->key === $key && $translation->locale === $locale);

            if ($match !== null) {
                return $match->value;
            }
        }

        return null;
    }

    protected function rememberTranslation(
        string $modelClass,
        int $modelId,
        string $key,
        string $locale,
        callable $resolver
    ): mixed {
        if (! config('translation.cache.enabled', true)) {
            return $resolver();
        }

        return cache()->rememberForever(
            TranslationCacheKey::for($modelClass, $modelId, $key, $locale),
            $resolver
        );
    }

    protected static function bootHasTranslation(): void
    {
        static::deleted(static function ($model): void {
            if (! in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
                $model->translationsPure()->delete();
                $model->forgetTranslationCache();
            }
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::forceDeleted(static function ($model): void {
                $model->translationsPure()->delete();
                $model->forgetTranslationCache();
            });
        }
    }

    public function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    protected function scopeWithTranslatedIn(Builder $query, ?string $locale = null): Builder
    {
        $locale ??= $this->getCurrentLocale();

        return $query->whereHas('translationsPure', function (Builder $q) use ($locale): void {
            $q->where('locale', $locale);
        });
    }

    protected function scopeWithTranslatedInForKey(
        Builder $query,
        string $key,
        ?string $locale = null
    ): Builder {
        $locale ??= $this->getCurrentLocale();

        return $query->whereHas('translationsPure', function (Builder $q) use ($key, $locale): void {
            $q->where('key', $key)
                ->where('locale', $locale);
        });
    }
}
