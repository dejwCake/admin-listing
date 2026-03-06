<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Exceptions\NotAModelClassException;
use Brackets\AdminListing\Services\AdminListingBuilder;
use Brackets\AdminListing\Tests\TestCase;
use Brackets\AdminListing\Tests\TestNotAModel;

class ExceptionsTest extends TestCase
{
    public function testCreatingListingForAClassThatIsNotAModelShouldLeadToAnException(): void
    {
        $builder = $this->app->make(AdminListingBuilder::class);

        try {
            $builder->for(TestNotAModel::class)->build();
        } catch (NotAModelClassException) {
            self::assertTrue(true);

            return ;
        }

        $this->fail('AdminListing should fail when trying to build for a non Model class');
    }

    public function testCreatingListingForAnIntegerClassShouldLeadToAnException(): void
    {
        $builder = $this->app->make(AdminListingBuilder::class);

        try {
            $builder->for((string) 10)->build();
        } catch (NotAModelClassException) {
            self::assertTrue(true);

            return ;
        }

        $this->fail('AdminListing should fail when trying to build for a non Model class');
    }

    public function testCreatingListingForANonClassStringShouldLeadToAnException(): void
    {
        $builder = $this->app->make(AdminListingBuilder::class);

        try {
            $builder->for("Some string that is definitely not a class name")->build();

            // this time we are not checking a NotAModelClassException exception,
            // because it is going to fail a bit earlier
        } catch (\Throwable) {
            self::assertTrue(true);

            return ;
        }

        self::fail('AdminListing should fail when trying to build for a non Model class');
    }
}
