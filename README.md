# Karnoweb Translation

پکیج دامنهٔ لاراول برای **ذخیرهٔ چندزبانهٔ فیلدهای مدل** به‌صورت polymorphic. لایهٔ داده فقط — بدون UI میزبان.

**مستندات:** [docs/README.md](docs/README.md) — [مفاهیم](docs/concepts/README.md) و [طرز استفاده](docs/usage/README.md)

## Requirements

- PHP 8.3+
- Laravel 13.x

## Installation

```bash
composer require karnoweb/translation:^13.0
php artisan vendor:publish --tag=translation-config       # اختیاری
php artisan vendor:publish --tag=translation-migrations   # الزامی
php artisan migrate
```

مایگریشن جدول ترجمه‌ها با نام ثابت `2022_01_01_*` پابلیش می‌شود.

## قابلیت‌ها

| قابلیت | نقطه ورود |
|--------|-----------|
| خواندن/نوشتن ترجمه | Trait `HasTranslation` |
| تنظیمات و مدل قابل‌تعویض | فاساد `Translation` |
| کش دائمی بر اساس کلید/لوکال | `translation.cache.enabled` |

**در پکیج نیست:** UI ادمین، سوییچر زبان، Policy، همگام‌سازی فایل‌های `lang/`.

## مثال سریع

```php
use Karnoweb\Translation\Concerns\HasTranslation;

class Brand extends Model
{
    use HasTranslation;

    /** @var list<string> */
    protected array $translatable = ['title', 'description'];
}

$brand->setTranslation('title', 'عنوان فارسی', 'fa');
$brand->setTranslation('title', 'English Title', 'en');

app()->setLocale('fa');
echo $brand->fresh()->title; // عنوان فارسی
```

جزئیات: [docs/usage/README.md](docs/usage/README.md)

## License

MIT
