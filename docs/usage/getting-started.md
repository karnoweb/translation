# نصب و راه‌اندازی

```bash
composer require karnoweb/translation:^13.0
php artisan vendor:publish --tag=translation-config      # اختیاری
php artisan vendor:publish --tag=translation-migrations  # الزامی برای استفاده در میزبان
php artisan migrate
```

مایگریشن‌ها با تاریخ ثابت `2022_01_01_*` پابلیش می‌شوند تا زود اجرا شوند و نام فایل‌ها پایدار بماند.

```env
TRANSLATION_TABLE=translations
TRANSLATION_MODEL=Karnoweb\Translation\Models\Translation
TRANSLATION_CACHE_ENABLED=true
```

لوکال اپ از تنظیمات خود لاراول می‌آید:

```env
APP_LOCALE=fa
APP_FALLBACK_LOCALE=en
```

برای پاک‌سازی کامل کش هنگام `forgetTranslationCache` بدون locale، بهتر است `app.supported_locales` را هم در config اپ تعریف کنید.

## اتصال به مدل میزبان

```php
use Illuminate\Database\Eloquent\Model;
use Karnoweb\Translation\Concerns\HasTranslation;

class Brand extends Model
{
    use HasTranslation;

    /** @var list<string> */
    protected array $translatable = [
        'title',
        'description',
    ];
}
```

اگر مدل ترجمه را در میزبان extend کرده‌اید:

```env
TRANSLATION_MODEL=App\Models\Translation
```

```php
Translation::model('translation'); // App\Models\Translation::class
```

## فاساد

```php
use Karnoweb\Translation\Facades\Translation;

Translation::config('table');
Translation::config('cache.enabled');
Translation::model('translation');
Translation::newModel('translation');

Translation::macro('ping', fn (): string => 'ok');
```

## قوانین

- پکیج UI و Policy ندارد؛ مسیر ادمین ترجمه را در اپ میزبان بسازید.
- مدل‌های پکیج Shop/Commerce که فیلد متنی دارند از همین trait استفاده می‌کنند.

## خطاها

خطاهای نصب معمولاً از خود لاراول‌اند (migration، config). اگر مدل در config نامعتبر باشد، `Translation::model()` استثنا می‌دهد.

## نتیجه ذخیره‌شده

بعد از migrate جدول `translations` (یا نام سفارشی) با ایندکس یکتا روی `(translatable_id, translatable_type, key, locale)` ساخته می‌شود.
