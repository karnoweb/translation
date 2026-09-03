# کش و fallback

وقتی `translation.cache.enabled` برابر `true` باشد (پیش‌فرض)، خواندن هر کلید برای هر لوکال با `rememberForever` کش می‌شود.

کلید کش تقریباً به این شکل است:

```text
translation:{ModelClass}:{id}:{key}:{locale}
```

## ترتیب خواندن `$model->title`

1. اگر رابطهٔ `translations` / `translationsPure` قبلاً لود شده باشد، از حافظه خوانده می‌شود.
2. در غیر این صورت از کش (یا DB و سپس پر کردن کش) برای **لوکال جاری**.
3. اگر مقدار خالی بود و `app.fallback_locale` با لوکال جاری فرق داشت، همان مسیر برای لوکال fallback تکرار می‌شود.

## باطل‌سازی

- `setTranslation` کش همان کلید/لوکال را پاک می‌کند و روابط لودشده را unset می‌کند.
- `forgetTranslationCache()` بدون آرگومان، برای همهٔ کلیدهای `$translatable` و لوکال‌های `app.supported_locales` (یا حداقل لوکال جاری) پاک می‌کند.

## نکتهٔ عملی

بعد از تغییر دسته‌ای ترجمه‌ها در ادمین، یا از `setTranslation` استفاده کنید یا صریحاً `forgetTranslationCache` بزنید تا فروشگاه مقدار کهنه نبیند.
