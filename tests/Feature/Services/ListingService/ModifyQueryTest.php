<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;

class ModifyQueryTest extends TestCase
{
    public function testModifyQueryFiltersResults(): void
    {
        $result = $this->listing
            ->modifyQuery(static function (Builder $query): void {
                $query->where('color', 'red');
            })
            ->get();

        self::assertCount(1, $result);
        self::assertEquals('Alpha', $result->first()->name);
    }

    public function testModifyQueryForTranslatedListingFiltersResults(): void
    {
        $result = $this->translatedListing
            ->modifyQuery(static function (Builder $query): void {
                $query->where('number', 999);
            })
            ->get();

        self::assertCount(1, $result);
        self::assertEquals('Alpha', $result->first()->name);
    }
}
