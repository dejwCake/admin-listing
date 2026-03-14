<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Exceptions\ModelNotTranslatableException;
use Brackets\AdminListing\Tests\TestCase;

class SetLocaleTest extends TestCase
{
    public function testSetLocaleOnNonTranslatableModelThrowsException(): void
    {
        $this->expectException(ModelNotTranslatableException::class);

        $this->listing->setLocale('en');
    }

    public function testSetLocaleOnTranslatableModelSetsLocale(): void
    {
        $result = $this->translatedListing
            ->setLocale('en')
            ->attachOrdering('name')
            ->get();

        self::assertCount(10, $result);
        self::assertEquals('Alpha', $result->first()->name);
    }

    public function testSetLocaleToSkChangesTranslatedValues(): void
    {
        $result = $this->translatedListing
            ->setLocale('sk')
            ->attachOrdering('number', 'desc')
            ->get();

        self::assertCount(10, $result);
        // First by number desc is number=999 (Alpha/Alfa)
        self::assertEquals('Alfa', $result->first()->name);
        self::assertEquals('cervena', $result->first()->color);
    }

    public function testSetLocaleReturnsListingForChaining(): void
    {
        $result = $this->translatedListing->setLocale('en');

        self::assertSame($this->translatedListing, $result);
    }
}
