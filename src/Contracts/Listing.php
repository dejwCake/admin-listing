<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Contracts;

use Brackets\AdminListing\Dtos\ListingQuery;
use Brackets\AdminListing\Exceptions\ModelNotTranslatableException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface Listing
{
    /**
     * Process listing query and get data
     *
     * If you need to include additional filters, you can manage it by
     * modifying a query using $modifyQuery function, which receives
     * a query as a parameter.
     *
     * If your model has translations, you can specify locale which should be loaded.
     * When searching and ordering, this locale will be appended to the query in appropriate places as well.
     *
     * This method does not perform any authorization nor validation.
     *
     * @throws ModelNotTranslatableException
     */
    public function processRequestAndGet(
        ListingQuery $listingQuery,
        ?callable $modifyQuery = null,
        ?string $locale = null,
    ): LengthAwarePaginator|Collection;

    /**
     * Set the locale you want to query
     *
     * @throws ModelNotTranslatableException
     */
    public function setLocale(string $locale): self;

    /**
     * Attach the ordering functionality
     */
    public function attachOrdering(string $orderBy, string $orderDirection = 'asc'): self;

    /**
     * Attach the searching functionality
     *
     * @param string|null $search searched string
     * @param array<string> $searchIn array of columns which should be searched in (only text, character
     * varying, or primary key are allowed)
     */
    public function attachSearch(?string $search, array $searchIn): self;

    /**
     * Attach the pagination functionality
     */
    public function attachPagination(int $currentPage, int $perPage = 10, string $pageColumnName = 'page'): self;

    /**
     * Modify a built query in any way
     */
    public function modifyQuery(callable $modifyQuery): self;

    /**
     * Execute query and get data
     *
     * @param array<string> $columns
     */
    public function get(array $columns = ['*']): LengthAwarePaginator|Collection;
}
