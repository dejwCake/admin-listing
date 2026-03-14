<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Tests\TestCase;

class AttachPaginationTest extends TestCase
{
    //TODO: Fix tests as we removed attachOrdering
    public function testListingProvidesPagination(): void
    {
        $result = $this->listing
            ->attachPagination(2, 3)
            ->get();

        self::assertCount(3, $result->getCollection());
        self::assertEquals(10, $result->total());
        self::assertEquals(3, $result->perPage());
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(4, $result->lastPage());
        self::assertEquals('Zeta 4', $result->getCollection()->first()->name);
    }

    public function testListingPaginationWorksOnTranslatableModelToo(): void
    {
        $result = $this->translatedListing
            ->attachPagination(2, 3)
            ->get();

        self::assertCount(3, $result->getCollection());
        self::assertEquals(10, $result->total());
        self::assertEquals(3, $result->perPage());
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(4, $result->lastPage());
        self::assertEquals('Zeta 4', $result->getCollection()->first()->name);
    }

    public function testListingPaginationWorksOnTranslatableModelWithLocaleSk(): void
    {
        $result = $this->translatedListing
            ->setLocale('sk')
            ->attachPagination(2, 3)
            ->get();

        self::assertCount(3, $result->getCollection());
        self::assertEquals(10, $result->total());
        self::assertEquals(3, $result->perPage());
        self::assertEquals(2, $result->currentPage());
        self::assertEquals(4, $result->lastPage());
        self::assertEquals('Zeta 4', $result->getCollection()->first()->name);
    }
}
