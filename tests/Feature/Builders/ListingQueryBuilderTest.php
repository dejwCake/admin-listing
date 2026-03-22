<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Builders;

use Brackets\AdminListing\Builders\ListingQueryBuilder;
use Brackets\AdminListing\Dtos\ListingQuery;
use Brackets\AdminListing\Tests\TestCase;
use Illuminate\Http\Request;
use Override;

class ListingQueryBuilderTest extends TestCase
{
    private ListingQueryBuilder $listingQueryBuilder;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->listingQueryBuilder = $this->app->make(ListingQueryBuilder::class);
    }

    public function testFromRequestWithDefaultParams(): void
    {
        $request = Request::create('/test', 'GET');

        $listingQuery = $this->listingQueryBuilder->fromRequest(
            $request,
            ['id', 'name'],
            ['name', 'color'],
        );

        self::assertInstanceOf(ListingQuery::class, $listingQuery);
        self::assertEquals(['id', 'name'], $listingQuery->columns);
        self::assertEquals(['name', 'color'], $listingQuery->searchIn);
        self::assertEquals('id', $listingQuery->orderBy);
        self::assertEquals('asc', $listingQuery->orderDirection);
        self::assertNull($listingQuery->search);
        self::assertEquals(1, $listingQuery->page);
        self::assertEquals(10, $listingQuery->perPage);
        self::assertFalse($listingQuery->bulk);
    }

    public function testFromRequestWithAllParams(): void
    {
        $request = Request::create('/test', 'GET', [
            'orderBy' => 'name',
            'orderDirection' => 'desc',
            'search' => 'test search',
            'page' => '3',
            'per_page' => '25',
            'bulk' => 'true',
        ]);

        $listingQuery = $this->listingQueryBuilder->fromRequest(
            $request,
            ['*'],
            ['name'],
        );

        self::assertEquals(['*'], $listingQuery->columns);
        self::assertEquals(['name'], $listingQuery->searchIn);
        self::assertEquals('name', $listingQuery->orderBy);
        self::assertEquals('desc', $listingQuery->orderDirection);
        self::assertEquals('test search', $listingQuery->search);
        self::assertEquals(3, $listingQuery->page);
        self::assertEquals(25, $listingQuery->perPage);
        self::assertTrue($listingQuery->bulk);
    }

    public function testFromRequestWithCustomDefaultOrderBy(): void
    {
        $request = Request::create('/test', 'GET');

        $listingQuery = $this->listingQueryBuilder->fromRequest(
            $request,
            ['*'],
            null,
            'created_at',
        );

        self::assertEquals('created_at', $listingQuery->orderBy);
    }

    public function testFromRequestWithNullSearchIn(): void
    {
        $request = Request::create('/test', 'GET');

        $listingQuery = $this->listingQueryBuilder->fromRequest($request, ['id', 'name']);

        self::assertNull($listingQuery->searchIn);
    }

    public function testFromRequestColumnsDefaultsToWildcard(): void
    {
        $request = Request::create('/test', 'GET');

        $listingQuery = $this->listingQueryBuilder->fromRequest($request);

        self::assertEquals(['*'], $listingQuery->columns);
    }
}
