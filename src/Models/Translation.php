<?php

declare(strict_types=1);

namespace Karnoweb\Translation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'translatable_id',
        'translatable_type',
        'key',
        'value',
        'locale',
    ];

    public function getTable(): string
    {
        return (string) config('translation.table', 'translations');
    }

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
