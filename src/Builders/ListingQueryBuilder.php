<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Builders;

use Brackets\AdminListing\Dtos\ListingQuery;
use Brackets\AdminListing\Enums\OrderDirection;
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
        OrderDirection $defaultOrderDirection = OrderDirection::Asc,
    ): ListingQuery {
        return new ListingQuery(
            columns: $columns,
            searchIn: $searchIn,
            orderBy: $request->input('orderBy', $defaultOrderBy),
            orderDirection: $this->resolveOrderDirection($request, $defaultOrderDirection)->value,
            search: $request->input('search'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', (int) $request->cookie('per_page', (string) 10)),
            bulk: (bool) $request->input('bulk'),
        );
    }

    private function resolveOrderDirection(Request $request, OrderDirection $default): OrderDirection
    {
        $orderDirection = $request->input('orderDirection');

        if (!is_string($orderDirection)) {
            return $default;
        }

        return OrderDirection::tryFrom($orderDirection) ?? $default;
    }
}
