<?php

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;
use Illuminate\Database\QueryException;

class OrderingTest extends TestCase
{
    public function testListingShouldProvideAbilityToSortByName()
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->get();

        self::assertEquals('Alpha', $result->first()->name);
    }

    public function listingShouldProvideAbilityToChangeSortOrder()
    {
        $result = $this->listing
            ->attachOrdering('name', 'desc')
            ->get();

        self::assertEquals('Alpha', $result->last()->name);
        self::assertEquals('Zeta 9', $result->first()->name);
    }

    public function testSortingByNotExistingColumnShouldLeadToAnError()
    {
        try {
            $this->listing
                ->attachOrdering('not_existing_column_name')
                ->get();
        } catch (QueryException $e) {
            self::assertTrue(true);
            return ;
        }

        $this->fail("Sorting by not existing column should lead to an exception");
    }

    public function testTranslatedListingCanBeSortedByTranslatedColumn()
    {
        $result = $this->translatedListing
            ->attachOrdering('name->en')
            ->get();

        $model = $result->first();

        self::assertEquals('2000-06-01 00:00:00', $model->published_at);
        self::assertEquals('Alpha', $model->name);
        self::assertEquals('red', $model->color);
        self::assertEquals('red', $model->getTranslation('color', 'en'));
    }

    public function testTranslatedListingSupportsQueryingOnlySomeColumns()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->get(['published_at', 'name']);

        $model = $result->first();

        self::assertEquals('2000-06-01 00:00:00', $model->published_at);
        self::assertEquals('Alpha', $model->name);
        self::assertEquals(null, $model->color);
        self::assertEquals('Alpha', $model->getTranslation('name', 'en'));
        self::assertEquals(null, $model->getTranslation('color', 'en'));
    }

    public function testTranslatedListingCanWorkWithLocales()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->get();

        self::assertCount(10, $result);

        $model = $result->first();

        self::assertEquals('2000-06-01 00:00:00', $model->published_at);
        self::assertEquals('Alfa', $model->name);
        self::assertEquals('cervena', $model->color);
        self::assertEquals('cervena', $model->getTranslation('color', 'sk'));
    }
}
