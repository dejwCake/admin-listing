<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Builders;

use Brackets\AdminListing\Dtos\ListingQuery;
use Illuminate\Http\Request;

final readonly class ListingQueryBuilder
{
    /**
     * @param array<string> $columns
     * @param array<string>|null $searchIn
     */
    public function fromRequest(
        Request $request,
        array $columns = ['*'],
        ?array $searchIn = null,
        string $defaultOrderBy = 'id',
    ): ListingQuery {
        return new ListingQuery(
            columns: $columns,
            searchIn: $searchIn,
            orderBy: $request->input('orderBy', $defaultOrderBy),
            orderDirection: $request->input('orderDirection', 'asc'),
            search: $request->input('search'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', (int) $request->cookie('per_page', (string) 10)),
            bulk: (bool) $request->input('bulk'),
        );
    }
}
