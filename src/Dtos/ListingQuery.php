<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Dtos;

final readonly class ListingQuery
{
    /**
     * @param array<string> $columns
     * @param array<string>|null $searchIn
     */
    public function __construct(
        public array $columns = ['*'],
        public ?array $searchIn = null,
        public string $orderBy = 'id',
        public string $orderDirection = 'asc',
        public ?string $search = null,
        public int $page = 1,
        public int $perPage = 10,
        public bool $bulk = false,
    ) {
    }
}
