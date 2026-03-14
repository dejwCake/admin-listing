<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Dtos\ListingQuery;
use Brackets\AdminListing\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProcessRequestAndGetTest extends TestCase
{
    public function testProcessRequestWithDefaultParams(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'name', 'number'],
            searchIn: ['name', 'color'],
        );

        $result = $this->listing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertCount(10, $result);
    }

    public function testProcessRequestWithOrdering(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'name', 'number'],
            searchIn: ['name', 'color'],
            orderBy: 'published_at',
            orderDirection: 'asc',
        );

        $result = $this->listing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertCount(10, $result);
        self::assertEquals('Zeta 2', $result->getCollection()->first()->name);
    }

    public function testProcessRequestWithSearch(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['*'],
            searchIn: ['name', 'color'],
            search: 'yellow a 1',
        );

        $result = $this->listing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertCount(1, $result);
        self::assertEquals('Zeta 10', $result->getCollection()->first()->name);
    }

    public function testProcessRequestWithPagination(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['*'],
            searchIn: ['name', 'color'],
            page: 2,
            perPage: 4,
        );

        $result = $this->listing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(3, $result->lastPage());
        self::assertCount(4, $result->getCollection());
        self::assertEquals('Zeta 5', $result->getCollection()->first()->name);
    }

    public function testProcessRequestWithModifyQuery(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['*'],
            searchIn: ['name', 'color'],
        );

        $result = $this->listing->processRequestAndGet(
            $listingQuery,
            static function (Builder $query): void {
                $query->where('color', 'red');
            },
        );

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertCount(1, $result);
    }

    public function testProcessRequestWithBulkReturnsOnlyIds(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['*'],
            searchIn: ['name', 'color'],
            bulk: true,
        );

        $result = $this->listing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(Collection::class, $result);
        self::assertCount(10, $result);
        self::assertArrayHasKey('id', $result->first()->toArray());
        self::assertArrayNotHasKey('name', $result->first()->toArray());
    }

    public function testProcessRequestOnTranslatableModelWithDefaultLocale(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'color'],
            searchIn: ['id', 'name', 'color'],
            search: 'a 1',
        );

        $result = $this->translatedListing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertEquals(2, $result->total());
        self::assertNull($result->getCollection()->first()->name);
        self::assertEquals('red', $result->getCollection()->first()->color);
    }

    public function testProcessRequestOnTranslatableModelWithSkLocale(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'color'],
            searchIn: ['id', 'name', 'color'],
            search: 'a 1',
        );

        $result = $this->translatedListing->processRequestAndGet($listingQuery, null, 'sk');

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertEquals(1, $result->total());
        self::assertNull($result->getCollection()->first()->name);
        self::assertEquals('cervena', $result->getCollection()->first()->color);
    }

    public function testProcessRequestOnTranslatableModelWithDefaultParams(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'name', 'color', 'number'],
            searchIn: ['name', 'color'],
        );

        $result = $this->translatedListing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertCount(10, $result);
    }

    public function testProcessRequestOnTranslatableModelWithPagination(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['id', 'name', 'color'],
            searchIn: ['name', 'color'],
            page: 2,
            perPage: 4,
        );

        $result = $this->translatedListing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(3, $result->lastPage());
        self::assertCount(4, $result->getCollection());
    }

    public function testProcessRequestOnTranslatableModelWithModifyQuery(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['*'],
            searchIn: ['name', 'color'],
        );

        $result = $this->translatedListing->processRequestAndGet(
            $listingQuery,
            static function (\Illuminate\Database\Eloquent\Builder $query): void {
                $query->where('number', 999);
            },
        );

        self::assertInstanceOf(LengthAwarePaginator::class, $result);
        self::assertCount(1, $result);
    }

    public function testProcessRequestOnTranslatableModelWithBulkReturnsOnlyIds(): void
    {
        $listingQuery = new ListingQuery(
            columns: ['*'],
            searchIn: ['name', 'color'],
            bulk: true,
        );

        $result = $this->translatedListing->processRequestAndGet($listingQuery);

        self::assertInstanceOf(Collection::class, $result);
        self::assertCount(10, $result);
        self::assertArrayHasKey('id', $result->first()->toArray());
    }
}
