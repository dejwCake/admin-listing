<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests\Feature\Services\ListingService;

use Brackets\AdminListing\Tests\TestCase;
use Brackets\AdminListing\Tests\TestModel;
use Brackets\AdminListing\Tests\TestTranslatableModel;

class AttachSearchTest extends TestCase
{
    public function testYouCanSearchAmongTextFieldsAndId(): void
    {
        $result = $this->listing
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForARepeatedTerm(): void
    {
        $result = $this->listing
            ->attachSearch('Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }

    public function testSearchingNotExistingQueryShouldReturnEmptyResponse(): void
    {
        $result = $this->listing
            ->attachSearch('not-existing-search-term', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingOnlyInColor(): void
    {
        $result = $this->listing
            ->attachSearch('Alpha', ['id', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingANumber(): void
    {
        $result = $this->listing
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(2, $result);
    }

    public function testTranslationsYouCanSearchAmongTextFieldsAndId(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testYouCannotSearchDependingOnADifferentLocale(): void
    {
        $result = $this->translatedListing
            ->setLocale('sk')
            ->attachSearch('Alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingANumberInTranslatedModel(): void
    {
        $result = $this->translatedListing
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(2, $result);
    }

    public function testSearchingANumberInTranslatedModelForSk(): void
    {
        $result = $this->translatedListing
            ->setLocale('sk')
            ->attachSearch((string) 1, ['id', 'name'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsZero(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Alpha Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchingForAMultipleTermsOne(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Zeta 1', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchingForAMultipleTermsMany(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Zeta yellow', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }

    public function testTranslatedSearchingForARepeatedTerm(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Zeta', ['id', 'name', 'color'])
            ->get();

        self::assertCount(9, $result);
    }

    public function testTranslatedSearchingNotExistingQueryShouldReturnEmptyResponse(): void
    {
        $result = $this->translatedListing
            ->attachSearch('not-existing-search-term', ['id', 'name', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testTranslatedSearchingOnlyInColor(): void
    {
        $result = $this->translatedListing
            ->attachSearch('Alpha', ['id', 'color'])
            ->get();

        self::assertCount(0, $result);
    }

    public function testSearchIsCaseInsensitive(): void
    {
        $result = $this->listing
            ->attachSearch('alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchIsCaseInsensitiveOnATranslatedColumn(): void
    {
        $result = $this->translatedListing
            ->attachSearch('alpha', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchIsAccentInsensitive(): void
    {
        if (!$this->supportsAccentInsensitiveSearch()) {
            self::markTestSkipped('Connection cannot match accented and unaccented text');
        }

        $this->createAccentedModel();

        $result = $this->listing
            ->attachSearch('ulozit', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchIsAccentInsensitiveOnATranslatedColumn(): void
    {
        if (!$this->supportsAccentInsensitiveSearch()) {
            self::markTestSkipped('Connection cannot match accented and unaccented text');
        }

        $this->createAccentedTranslatableModel();

        $result = $this->translatedListing
            ->attachSearch('ulozit', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    public function testSearchStillMatchesTheAccentedSpellingItself(): void
    {
        $this->createAccentedModel();

        $result = $this->listing
            ->attachSearch('Uložiť', ['id', 'name', 'color'])
            ->get();

        self::assertCount(1, $result);
    }

    private function createAccentedModel(): void
    {
        TestModel::create([
            'name' => 'Uložiť',
            'color' => 'modrá',
            'number' => 777,
            'published_at' => '2000-06-01 00:00:00',
        ]);
    }

    private function createAccentedTranslatableModel(): void
    {
        TestTranslatableModel::create([
            'name' => ['en' => 'Uložiť', 'sk' => 'Uložiť'],
            'color' => ['en' => 'modrá', 'sk' => 'modrá'],
            'number' => 777,
            'published_at' => '2000-06-01 00:00:00',
        ]);
    }
}
