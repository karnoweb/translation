# معماری پکیج

پکیج یک **لایهٔ دادهٔ ترجمهٔ polymorphic** است، نه اپلیکیشن چندزبانهٔ کامل. کنترلر، Livewire ادمین، middleware تعویض زبان و Policy داخل پکیج نیستند.

## لایه‌ها

| لایه | مسیر | نقش |
|------|------|------|
| Concern | `src/Concerns/HasTranslation.php` | خواندن/نوشتن ترجمه روی مدل میزبان |
| Model | `src/Models/Translation.php` | ردیف جدول ترجمه‌ها |
| Manager | `src/Translation.php` | تنظیمات و resolve مدل از config |
| Support | `src/Support/` | کلید کش و resolve مدل‌های پیکربندی‌شده |
| Facade | `src/Facades/Translation.php` | دسترسی راحت به Manager |

قواعد:

1. فیلدهای ترجمه‌پذیر را در `$translatable` مدل اعلام کنید؛ مقدار از ستون جدول میزبان خوانده نمی‌شود.
2. `setTranslation` را فقط بعد از ذخیرهٔ مدل (وجود `id`) صدا بزنید.
3. مدل جدول ترجمه را می‌توانید در میزبان subclass کنید و با `TRANSLATION_MODEL` معرفی کنید.
4. پکیج به دامنهٔ Shop/Commerce/CRM وابسته نیست؛ فقط Illuminate و PHP.

## بیرون از scope پکیج

UI ترجمه، سوییچر لوکال، همگام‌سازی فایل‌های `lang/`، API REST اختصاصی ترجمه. این‌ها مسئولیت اپ میزبان‌اند.
