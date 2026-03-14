<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Unit\Dtos;

use Brackets\AdminListing\Dtos\ListingQuery;
use PHPUnit\Framework\TestCase;

class ListingQueryTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $listingQuery = new ListingQuery();

        self::assertEquals(['*'], $listingQuery->columns);
        self::assertNull($listingQuery->searchIn);
        self::assertEquals('id', $listingQuery->orderBy);
        self::assertEquals('asc', $listingQuery->orderDirection);
        self::assertNull($listingQuery->search);
        self::assertEquals(1, $listingQuery->page);
        self::assertEquals(10, $listingQuery->perPage);
        self::assertFalse($listingQuery->bulk);
    }

    public function testCustomValues(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'name'],
            searchIn: ['name', 'email'],
            orderBy: 'name',
            orderDirection: 'desc',
            search: 'test',
            page: 2,
            perPage: 25,
            bulk: true,
        );

        self::assertEquals(['id', 'name'], $listingQuery->columns);
        self::assertEquals(['name', 'email'], $listingQuery->searchIn);
        self::assertEquals('name', $listingQuery->orderBy);
        self::assertEquals('desc', $listingQuery->orderDirection);
        self::assertEquals('test', $listingQuery->search);
        self::assertEquals(2, $listingQuery->page);
        self::assertEquals(25, $listingQuery->perPage);
        self::assertTrue($listingQuery->bulk);
    }
}
