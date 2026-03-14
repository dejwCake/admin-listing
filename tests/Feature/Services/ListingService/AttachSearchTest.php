<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Tests\TestCase;

class AttachSearchTest extends TestCase
{
    //TODO have same use cases for listing and translatedListing
    public function testYouCanSearchAmongTextFieldsAndId(): void
    {
        $result = $this->listing
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForARepeatedTerm(): void
    {
        $result = $this->listing
            ->attachSearch('Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }

    public function testSearchingNotExistingQueryShouldReturnEmptyResponse(): void
    {
        $result = $this->listing
            ->attachSearch('not-existing-search-term', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingOnlyInColor(): void
    {
        $result = $this->listing
            ->attachSearch('Alpha', ['id', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingANumber(): void
    {
        $result = $this->listing
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(2, $result);
    }

    public function testTranslationsYouCanSearchAmongTextFieldsAndId(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testYouCannotSearchDependingOnADifferentLocale(): void
    {
        $result = $this->translatedListing
            ->setLocale('sk')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingANumberInTranslatedModel(): void
    {
        $result = $this->translatedListing
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(2, $result);
    }

    public function testSearchingANumberInTranslatedModelForSk(): void
    {
        $result = $this->translatedListing
            ->setLocale('sk')
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsZero(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Alpha Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingForAMultipleTermsOne(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Zeta 1', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsMany(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Zeta yellow', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }
}
