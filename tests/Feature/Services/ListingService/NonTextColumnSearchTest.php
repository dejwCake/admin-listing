<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Tests\TestCase;

class NonTextColumnSearchTest extends TestCase
{
    public function testSearchingInAnIntegerColumn(): void
    {
        $result = $this->listing
            ->attachSearch('999', ['name', 'number'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingInADateColumn(): void
    {
        $result = $this->listing
            ->attachSearch('2000-06', ['name', 'published_at'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingInAnIntegerColumnOnATranslatedModel(): void
    {
        $result = $this->translatedListing
            ->attachSearch('999', ['name', 'number'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingInADateColumnOnATranslatedModel(): void
    {
        $result = $this->translatedListing
            ->attachSearch('2000-06', ['name', 'published_at'])
            ->get();

        self::assertCount(1, $result);
    }
}
