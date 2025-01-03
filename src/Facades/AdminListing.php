<?php

namespace Brackets\AdminListing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Brackets\AdminListing\AdminListing
 */
class AdminListing extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    protected static function getFacadeAccessor()
    {
        return 'admin-listing';
    }
}
