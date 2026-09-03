# نوشتن و خواندن ترجمه

```php
$brand = Brand::query()->create([/* ... */]);

$brand->setTranslation('title', 'عنوان فارسی', 'fa');
$brand->setTranslation('title', 'English Title', 'en');
$brand->setTranslation('description', 'توضیح کوتاه', 'fa');

app()->setLocale('fa');
echo $brand->fresh()->title; // عنوان فارسی

app()->setLocale('en');
echo $brand->fresh()->title; // English Title
```

اگر لوکال را به `setTranslation` ندهید، از `app()->getLocale()` استفاده می‌شود.

## ادمین — همهٔ لوکال‌ها

```php
$brand->translationsPure()
    ->where('key', 'title')
    ->get();

$brand->translations(); // فقط لوکال جاری
```

## قوانین

- مدل باید `id` داشته باشد؛ قبل از `save`/`create`، `setTranslation` عملاً کاری نمی‌کند.
- فقط کلیدهای داخل `$translatable` از مسیر magic attribute ترجمه می‌شوند.
- برای به‌روزرسانی همان کلید/لوکال، دوباره `setTranslation` بزنید (updateOrCreate).

## خطاها

| وضعیت | رفتار |
|--------|--------|
| مدل بدون `id` | `setTranslation` بدون ذخیره برمی‌گردد |
| کلید خارج از `$translatable` | مثل attribute معمولی Eloquent رفتار می‌کند (ترجمه نمی‌شود) |

## نتیجه ذخیره‌شده

یک ردیف در جدول ترجمه‌ها با `translatable_type` برابر morph class مدل، به‌همراه `key`، `value`، `locale`. timestamps ندارد.
