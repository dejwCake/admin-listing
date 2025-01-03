<?php

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;

class BasicStuffTest extends TestCase
{
    public function testListingShouldReturnWholeCollectionWhenNothingWasSet()
    {
        $result = $this->listing
            ->get();

        $this->assertCount(10, $result);
        $model = $result->first();
        $this->assertArrayHasKey('id', $model);
        $this->assertArrayHasKey('name', $model);
        $this->assertArrayHasKey('color', $model);
        $this->assertArrayHasKey('number', $model);
        $this->assertArrayHasKey('published_at', $model);
    }

    public function testListingAbilityToSpecifyColumnsToFilter()
    {
        $result = $this->listing
            ->get(['name', 'color']);

        $this->assertCount(10, $result);
        $model = $result->first();
        $this->assertArrayNotHasKey('id', $model);
        $this->assertArrayHasKey('name', $model);
        $this->assertArrayHasKey('color', $model);
        $this->assertArrayNotHasKey('number', $model);
        $this->assertArrayNotHasKey('published_at', $model);
    }

    public function testItShouldBePossibleToRunSameQueryTwice()
    {
        $this->listing
            ->get();

        $result = $this->listing
            ->get();

        $this->assertCount(10, $result);
        $model = $result->first();
        $this->assertArrayHasKey('id', $model);
        $this->assertArrayHasKey('name', $model);
        $this->assertArrayHasKey('color', $model);
        $this->assertArrayHasKey('number', $model);
        $this->assertArrayHasKey('published_at', $model);
    }
}
