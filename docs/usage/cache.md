# کش

```php
use Illuminate\Support\Facades\Cache;
use Karnoweb\Translation\Support\TranslationCacheKey;

$brand->setTranslation('title', 'اول', 'fa');
// خواندن بعدی کش را پر می‌کند
$brand->fresh()->title;

$key = TranslationCacheKey::for(Brand::class, (int) $brand->id, 'title', 'fa');
Cache::has($key); // true وقتی cache.enabled=true

$brand->setTranslation('title', 'دوم', 'fa'); // کش همان کلید باطل می‌شود

// پاک‌سازی دستی
$brand->forgetTranslationCache();                 // همهٔ کلیدهای translatable
$brand->forgetTranslationCache('title', 'fa');    // یک کلید/لوکال
```

غیرفعال کردن کش:

```env
TRANSLATION_CACHE_ENABLED=false
```

## قوانین

- با کش فعال، بعد از ویرایش خام روی مدل `Translation` حتماً کش را فراموش کنید؛ مسیر امن همان `setTranslation` است.
- `supported_locales` را در اپ تنظیم کنید تا forget بدون locale همهٔ زبان‌ها را پوشش دهد.

## خطاها

خطای دامنهٔ جداگانه‌ای برای کش تعریف نشده است.

## نتیجه ذخیره‌شده

کش فقط در store کش اپ است؛ جدول DB با forget عوض نمی‌شود.
