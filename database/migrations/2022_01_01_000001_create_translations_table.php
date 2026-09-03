<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $table = (string) config('translation.table', 'translations');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table): void {
            $table->id();
            $table->morphs('translatable');
            $table->string('key');
            $table->text('value');
            $table->string('locale');
            $table->unique([
                'translatable_id',
                'translatable_type',
                'key',
                'locale',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists((string) config('translation.table', 'translations'));
    }
};
