<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Builders;

use Brackets\AdminListing\Builders\ListingBuilder;
use Brackets\AdminListing\Contracts\Listing;
use Brackets\AdminListing\Exceptions\NotAModelClassException;
use Brackets\AdminListing\Tests\TestCase;
use Brackets\AdminListing\Tests\TestModel;
use Brackets\AdminListing\Tests\TestNotAModel;
use Brackets\AdminListing\Tests\TestTranslatableModel;
use Throwable;

class ListingBuilderTest extends TestCase
{
    private ListingBuilder $listingBuilder;

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->listingBuilder = $this->app->make(ListingBuilder::class);
    }

    public function testBuildWithStringClassReturnsListing(): void
    {
        $listing = $this->listingBuilder->for(TestModel::class)->build();

        self::assertInstanceOf(Listing::class, $listing);
    }

    public function testBuildWithModelInstanceReturnsListing(): void
    {
        $model = new TestModel();

        $listing = $this->listingBuilder->for($model)->build();

        self::assertInstanceOf(Listing::class, $listing);
    }

    public function testBuildWithTranslatableModelReturnsListing(): void
    {
        $listing = $this->listingBuilder->for(TestTranslatableModel::class)->build();

        self::assertInstanceOf(Listing::class, $listing);
    }

    public function testBuildWithoutModelThrowsException(): void
    {
        $this->expectException(NotAModelClassException::class);
        $this->expectExceptionMessage('Model must be set before building Listing');

        $this->listingBuilder->build();
    }

    public function testBuildWithNonModelClassThrowsException(): void
    {
        $this->expectException(NotAModelClassException::class);

        $this->listingBuilder->for(TestNotAModel::class)->build();
    }

    public function testBuildWithIntegerStringThrowsException(): void
    {
        $this->expectException(NotAModelClassException::class);

        $this->listingBuilder->for((string) 10)->build();
    }

    public function testBuildWithNonClassStringThrowsException(): void
    {
        $this->expectException(Throwable::class);

        $this->listingBuilder->for('Some string that is definitely not a class name')->build();
    }

    public function testForReturnsImmutableClone(): void
    {
        $clone = $this->listingBuilder->for(TestModel::class);

        self::assertNotSame($this->listingBuilder, $clone);
    }

    public function testForDoesNotMutateOriginalBuilder(): void
    {
        $this->listingBuilder->for(TestModel::class);

        $this->expectException(NotAModelClassException::class);
        $this->expectExceptionMessage('Model must be set before building Listing');

        $this->listingBuilder->build();
    }

    public function testBuildReturnsWorkingListing(): void
    {
        $listing = $this->listingBuilder->for(TestModel::class)->build();

        $result = $listing->get();

        self::assertCount(10, $result);
    }

    public function testBuildWithNullTranslationsConfigReturnsNonTranslatableListing(): void
    {
        $this->app['config']->set('admin-listing.with-translations-class', null);
        $builder = $this->app->make(ListingBuilder::class);

        $listing = $builder->for(TestTranslatableModel::class)->build();

        //TODO need to throw exception if translatable functionality called
        self::assertInstanceOf(Listing::class, $listing);
    }
}
