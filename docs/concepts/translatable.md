# فیلدهای ترجمه‌پذیر

ترجمه‌ها ستون روی جدول مدل نیستند. هر مقدار در جدول `translations` (قابل تنظیم) با ترکیب یکتا ذخیره می‌شود:

`(translatable_type, translatable_id, key, locale) → value`

## مدل ذهنی

```text
Brand (id=5)
  ├── title@fa  → «برند نمونه»
  ├── title@en  → «Sample Brand»
  └── description@fa → «توضیح…»
```

مدل میزبان با trait اعلام می‌کند کدام کلیدها ترجمه‌پذیرند:

```php
protected array $translatable = ['title', 'description', 'body'];
```

دسترسی `$model->title` از `getAttribute` trait می‌آید و مقدار لوکال جاری (با fallback) را برمی‌گرداند.

## روابط

| رابطه | معنا |
|--------|------|
| `translations()` | فقط ردیف‌های **لوکال جاری** (`app()->getLocale()`) |
| `translationsPure()` | همهٔ لوکال‌ها — مناسب ادمین و fallback |

## حذف مدل

- حذف سخت یا `forceDelete`: ردیف‌های ترجمه و کش پاک می‌شوند.
- Soft delete معمولی: ترجمه‌ها **می‌مانند** تا با restore قابل استفاده‌اند.

## ایندکس یکتا

یک مدل نمی‌تواند دو مقدار برای همان `key` و `locale` داشته باشد؛ `setTranslation` با `updateOrCreate` کار می‌کند.
