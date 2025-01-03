<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;

class PaginationTest extends TestCase
{
    public function testListingProvidesPagination(): void
    {
        $result = $this->listing
            ->attachOrdering('name')
            ->attachPagination(2, 3)
            ->get();

        self::assertCount(3, $result->getCollection());
        self::assertEquals(10, $result->total());
        self::assertEquals(3, $result->perPage());
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(4, $result->lastPage());
        self::assertEquals('Zeta 3', $result->getCollection()->first()->name);
    }

    public function testListingPaginationWorksOnTranslatableModelToo(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->attachPagination(2, 3)
            ->get();

        self::assertCount(3, $result->getCollection());
        self::assertEquals(10, $result->total());
        self::assertEquals(3, $result->perPage());
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(4, $result->lastPage());
        self::assertEquals('Zeta 3', $result->getCollection()->first()->name);
    }

    public function testListingPaginationWorksOnTranslatableModelWithLocaleSk(): void
    {
        $result = $this->translatedListing
            ->attachOrdering('name')
            ->setLocale('sk')
            ->attachPagination(1, 3)
            ->get();

        self::assertCount(3, $result->getCollection());
        self::assertEquals(10, $result->total());
        self::assertEquals(3, $result->perPage());
        self::assertEquals(1, $result->currentPage());
        self::assertEquals(4, $result->lastPage());
        self::assertEquals('Alfa', $result->getCollection()->first()->name);
    }
}
