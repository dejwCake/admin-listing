<?php

namespace Brackets\AdminListing\Tests;

use Brackets\Translatable\Models\WithTranslations;
use Brackets\Translatable\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TestTranslatableModel extends Model implements WithTranslations
{
    use HasTranslations;

    protected $table = 'test_translatable_models';
    protected $guarded = [];
    public $timestamps = false;

    // these attributes are translatable
    public $translatable = [
        'name',
        'color',
    ];
}
