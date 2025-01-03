<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;

class SearchTest extends TestCase
{
    public function testYouCanSearchAmongTextFieldsAndId(): void
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForARepeatedTerm(): void
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }

    public function testSearchingNotExistingQueryShouldReturnEmptyResponse(): void
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('not-existing-search-term', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingOnlyInColor(): void
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('Alpha', ['id', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingANumber(): void
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(2, $result);
    }

    public function testTranslationsYouCanSearchAmongTextFieldsAndId(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testYouCannotSearchDependingOnADifferentLocale(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingANumberInTranslatedModel(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(2, $result);
    }

    public function testSearchingANumberInTranslatedModelForSk(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsZero(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Alpha Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingForAMultipleTermsOne(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Zeta 1', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsMany(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Zeta yellow', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }
}
