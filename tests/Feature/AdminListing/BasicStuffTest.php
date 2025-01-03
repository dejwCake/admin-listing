<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;

class BasicStuffTest extends TestCase
{
    public function testListingShouldReturnWholeCollectionWhenNothingWasSet(): void
    {
        $result = $this->listing
            ->get();

        self::assertCount(10, $result);
        $model = $result->first();
        self::assertArrayHasKey('id', $model);
        self::assertArrayHasKey('name', $model);
        self::assertArrayHasKey('color', $model);
        self::assertArrayHasKey('number', $model);
        self::assertArrayHasKey('published_at', $model);
    }

    public function testListingAbilityToSpecifyColumnsToFilter(): void
    {
        $result = $this->listing
            ->get(['name', 'color']);

        self::assertCount(10, $result);
        $model = $result->first();
        self::assertArrayNotHasKey('id', $model);
        self::assertArrayHasKey('name', $model);
        self::assertArrayHasKey('color', $model);
        self::assertArrayNotHasKey('number', $model);
        self::assertArrayNotHasKey('published_at', $model);
    }

    public function testItShouldBePossibleToRunSameQueryTwice(): void
    {
        $this->listing
            ->get();

        $result = $this->listing
            ->get();

        self::assertCount(10, $result);
        $model = $result->first();
        self::assertArrayHasKey('id', $model);
        self::assertArrayHasKey('name', $model);
        self::assertArrayHasKey('color', $model);
        self::assertArrayHasKey('number', $model);
        self::assertArrayHasKey('published_at', $model);
    }
}
