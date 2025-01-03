<?php

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;

class SearchTest extends TestCase
{
    public function testYouCanSearchAmongTextFieldsAndId()
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(1, $result);
    }

    public function testSearchingForARepeatedTerm()
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('Zeta', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(9, $result);
    }

    public function testSearchingNotExistingQueryShouldReturnEmptyResponse()
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('not-existing-search-term', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(0, $result);
    }

    public function testSearchingOnlyInColor()
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch('Alpha', ['id', 'color'])
            ->get();

        $this->assertCount(0, $result);
    }

    public function testSearchingANumber()
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachSearch(1, ['id', 'name'])
            ->get();

        $this->assertCount(2, $result);
    }

    public function testTranslationsYouCanSearchAmongTextFieldsAndId()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(1, $result);
    }

    public function testYouCannotSearchDependingOnADifferentLocale()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(0, $result);
    }

    public function testSearchingANumberInTranslatedModel()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch(1, ['id', 'name'])
            ->get();

        $this->assertCount(2, $result);
    }

    public function testSearchingANumberInTranslatedModelForSk()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->attachSearch(1, ['id', 'name'])
            ->get();

        $this->assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsZero()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Alpha Zeta', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(0, $result);
    }

    public function testSearchingForAMultipleTermsOne()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Zeta 1', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsMany()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachSearch('Zeta yellow', ['id', 'name', 'color'])
            ->get();

        $this->assertCount(9, $result);
    }
}
