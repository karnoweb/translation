<?php

declare(strict_types=1);

namespace Karnoweb\Translation\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Karnoweb\Translation\Concerns\HasTranslation;
use Karnoweb\Translation\Support\TranslationCacheKey;
use Karnoweb\Translation\Tests\TestCase;

final class HasTranslationBehaviorTest extends TestCase
{
    use RefreshDatabase;

    /** @var class-string<Model> */
    private string $modelClass;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_translatables', function ($table): void {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->modelClass = get_class(new class extends Model {
            use HasTranslation;
            use SoftDeletes;

            protected $table = 'test_translatables';

            /** @var list<string> */
            protected array $translatable = ['title'];
        });
    }

    public function test_set_translation_forgets_and_repopsulates_cache(): void
    {
        Cache::flush();

        $model = $this->modelClass::query()->create([]);

        $model->setTranslation('title', 'First', 'en');
        $this->assertSame('First', $model->fresh()->title);

        $cacheKey = TranslationCacheKey::for($this->modelClass, (int) $model->id, 'title', 'en');
        $this->assertTrue(Cache::has($cacheKey));

        $model->setTranslation('title', 'Second', 'en');
        $this->assertSame('Second', $model->fresh()->title);
        $this->assertSame('Second', Cache::get($cacheKey));
    }

    public function test_with_translated_in_scope_uses_requested_locale(): void
    {
        $english = $this->modelClass::query()->create([]);
        $english->setTranslation('title', 'English', 'en');

        $persian = $this->modelClass::query()->create([]);
        $persian->setTranslation('title', 'Persian', 'fa');

        app()->setLocale('en');

        $results = $this->modelClass::query()->withTranslatedIn('fa')->pluck('id')->all();

        $this->assertSame([(int) $persian->id], $results);
    }

    public function test_soft_delete_does_not_remove_translations(): void
    {
        $model = $this->modelClass::query()->create([]);
        $model->setTranslation('title', 'Keep me', 'en');

        $model->delete();

        $this->assertDatabaseHas('translations', [
            'translatable_id' => $model->id,
            'key' => 'title',
            'locale' => 'en',
            'value' => 'Keep me',
        ]);
    }

    public function test_force_delete_removes_translations(): void
    {
        $model = $this->modelClass::query()->create([]);
        $model->setTranslation('title', 'Remove me', 'en');

        $model->forceDelete();

        $this->assertDatabaseMissing('translations', [
            'translatable_id' => $model->id,
            'key' => 'title',
            'locale' => 'en',
        ]);
    }
}
