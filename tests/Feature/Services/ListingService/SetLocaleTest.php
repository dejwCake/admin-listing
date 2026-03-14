<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Exceptions\ModelNotTranslatableException;
use Brackets\AdminListing\Tests\TestCase;

class SetLocaleTest extends TestCase
{
    //TODO add more test to cover positive scenario

    public function testSetLocaleOnNonTranslatableModelThrowsException(): void
    {
        $this->expectException(ModelNotTranslatableException::class);

        $this->listing->setLocale('en');
    }
}
