<?php

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\AdminListing;
use Brackets\AdminListing\Exceptions\NotAModelClassException;
use Brackets\AdminListing\Tests\TestCase;
use Brackets\AdminListing\Tests\TestNotAModel;

class ExceptionsTest extends TestCase
{
    public function testCreatingListingForAClassThatIsNotAModelShouldLeadToAnException(): void
    {
        try {
            AdminListing::create(TestNotAModel::class);
        } catch (NotAModelClassException $e) {
            self::assertTrue(true);
            return ;
        }

        $this->fail('AdminListing should fail when trying to build for a non Model class');
    }

    public function testCreatingListingForAnIntegerClassShouldLeadToAnException(): void
    {
        try {
            AdminListing::create(10);
        } catch (NotAModelClassException $e) {
            self::assertTrue(true);
            return ;
        }

        $this->fail('AdminListing should fail when trying to build for a non Model class');
    }

    public function testCreatingListingForANonClassStringShouldLeadToAnException(): void
    {
        try {
            AdminListing::create("Some string that is definitely not a class name");

            // this time we are not checking a NotAModelClassException exception, because it is going to fail a bit earlier
        } catch (\Exception $e) {
            self::assertTrue(true);
            return ;
        }

        self::fail('AdminListing should fail when trying to build for a non Model class');
    }
}
