<?php

namespace Brackets\AdminListing\Tests;

use Brackets\Translatable\Models\WithTranslations;
use Brackets\Translatable\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TestTranslatableModel extends Model implements WithTranslations
{
    use HasTranslations;

    /**
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $table = 'test_translatable_models';

    /**
     * @var array<string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $guarded = [];

    /**
     * @var bool
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    public $timestamps = false;

    // these attributes are translatable
    public $translatable = [
        'name',
        'color',
    ];
}
