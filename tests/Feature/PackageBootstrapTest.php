<?php

declare(strict_types=1);

namespace Karnoweb\Translation\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Karnoweb\Translation\Concerns\HasTranslation;
use Karnoweb\Translation\Facades\Translation as TranslationFacade;
use Karnoweb\Translation\Models\Translation;
use Karnoweb\Translation\Tests\TestCase;
use Karnoweb\Translation\Translation as TranslationManager;
use Karnoweb\Translation\TranslationServiceProvider;

final class PackageBootstrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_provider_is_registered(): void
    {
        $this->assertTrue($this->app->providerIsLoaded(TranslationServiceProvider::class));
    }

    public function test_translation_singleton_and_facade_resolve(): void
    {
        $this->assertTrue($this->app->bound('translation'));
        $this->assertSame($this->app->make('translation'), $this->app->make('translation'));
        $this->assertInstanceOf(TranslationManager::class, TranslationFacade::getFacadeRoot());
        $this->assertSame('translations', TranslationFacade::config('table'));
    }

    public function test_translations_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('translations'));
        $this->assertTrue(Schema::hasColumns('translations', [
            'translatable_id',
            'translatable_type',
            'key',
            'value',
            'locale',
        ]));
    }

    public function test_has_translation_trait_resolves_attribute(): void
    {
        $modelClass = get_class(new class extends Model {
            use HasTranslation;

            protected $table = 'test_translatables';

            public $timestamps = false;

            /** @var list<string> */
            protected array $translatable = ['title'];
        });

        Schema::create('test_translatables', function ($table): void {
            $table->id();
        });

        $record = $modelClass::query()->create([]);

        Translation::query()->create([
            'translatable_id' => $record->id,
            'translatable_type' => $record->getMorphClass(),
            'key' => 'title',
            'value' => 'Test Title',
            'locale' => 'en',
        ]);

        $this->assertSame('Test Title', $record->fresh()->title);
    }

    public function test_translation_model_resolver(): void
    {
        $this->assertSame(Translation::class, TranslationFacade::model('translation'));
    }

    public function test_translation_supports_macros(): void
    {
        TranslationFacade::macro('testMacro', fn (): string => 'ok');

        $this->assertSame('ok', TranslationFacade::testMacro());
    }
}
