<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Brackets\AdminListing\Services\AdminListingService
 * @deprecated We do not want to support Facades anymore. Please use dependency injection instead.
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
