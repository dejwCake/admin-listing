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

        $this->assertEquals('Alpha', $result->first()->name);
    }

    public function listingShouldProvideAbilityToChangeSortOrder()
    {
        $result = $this->listing
            ->attachOrdering('name', 'desc')
            ->get();

        $this->assertEquals('Alpha', $result->last()->name);
        $this->assertEquals('Zeta 9', $result->first()->name);
    }

    public function testSortingByNotExistingColumnShouldLeadToAnError()
    {
        try {
            $this->listing
                ->attachOrdering('not_existing_column_name')
                ->get();
        } catch (QueryException $e) {
            $this->assertTrue(true);
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

        $this->assertEquals('2000-06-01 00:00:00', $model->published_at);
        $this->assertEquals('Alpha', $model->name);
        $this->assertEquals('red', $model->color);
        $this->assertEquals('red', $model->getTranslation('color', 'en'));
    }

    public function testTranslatedListingSupportsQueryingOnlySomeColumns()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->get(['published_at', 'name']);

        $model = $result->first();

        $this->assertEquals('2000-06-01 00:00:00', $model->published_at);
        $this->assertEquals('Alpha', $model->name);
        $this->assertEquals(null, $model->color);
        $this->assertEquals('Alpha', $model->getTranslation('name', 'en'));
        $this->assertEquals(null, $model->getTranslation('color', 'en'));
    }

    public function testTranslatedListingCanWorkWithLocales()
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->get();

        $this->assertCount(10, $result);

        $model = $result->first();

        $this->assertEquals('2000-06-01 00:00:00', $model->published_at);
        $this->assertEquals('Alfa', $model->name);
        $this->assertEquals('cervena', $model->color);
        $this->assertEquals('cervena', $model->getTranslation('color', 'sk'));
    }
}
