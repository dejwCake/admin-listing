<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Services;

use Brackets\AdminListing\Contracts\AdminListing;
use Brackets\AdminListing\Exceptions\ModelNotTranslatableException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class AdminListingService implements AdminListing
{
    private Builder $query;

    private int $currentPage;

    private int $perPage;

    private string $pageColumnName = 'page';

    private bool $hasPagination = false;

    private string $locale;

    private string $orderBy;

    private string $orderDirection = 'asc';

    private ?string $search;

    /** @var array<string> */
    private array $searchIn = [];

    public function __construct(
        private readonly DatabaseManager $databaseManager,
        private readonly Model $model,
        private readonly bool $modelHasTranslations = false,
        string $defaultLocale = 'en',
    ) {
        if ($this->modelHasTranslations) {
            $this->locale = $this->model->locale ?: $defaultLocale;
        }

        $this->query = $this->model->newQuery();
        $this->orderBy = $this->model->getKeyName();
    }

    /**
     * @return LengthAwarePaginator|Collection LengthAwarePaginator when pagination was attached, Collection otherwise
     */
    public function processRequestAndGet(
        Request $request,
        array $columns = ['*'],
        ?array $searchIn = null,
        ?callable $modifyQuery = null,
        ?string $locale = null,
    ): LengthAwarePaginator|Collection {
        $this->attachOrdering(
            $request->input('orderBy', $this->model->getKeyName()),
            $request->input('orderDirection', 'asc'),
        )->attachSearch(
            $request->input('search'),
            $searchIn,
        );

        // attach pagination only when bulk filter is disabled
        if (!$request->input('bulk')) {
            $this->attachPagination(
                (int) $request->input('page', 1),
                (int) $request->input('per_page', (int) $request->cookie('per_page', (string) 10)),
            );
        }

        if ($modifyQuery !== null) {
            $this->modifyQuery($modifyQuery);
        }

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        // bulk filter enabled — return only primary keys
        if ($request->input('bulk')) {
            return $this->get(['id']);
        }

        return $this->get($columns);
    }

    /**
     * This method is only valid for Translatable models
     *
     * @throws ModelNotTranslatableException
     */
    public function setLocale(string $locale): self
    {
        if (!$this->modelHasTranslations) {
            throw new ModelNotTranslatableException('Model is not translatable, so you cannot set locale');
        }
        $this->locale = $locale;

        return $this;
    }

    /**
     * Any repeated call to this method is going to have no effect and original ordering is going to be used.
     * This is due to the limitation of the Illuminate\Database\Eloquent\Builder.
     */
    public function attachOrdering(string $orderBy, string $orderDirection = 'asc'): self
    {
        $this->orderBy = $orderBy;
        $this->orderDirection = $orderDirection;

        return $this;
    }

    public function attachSearch(?string $search, array $searchIn): self
    {
        $this->search = $search;
        $this->searchIn = $searchIn;

        return $this;
    }

    public function attachPagination(int $currentPage, int $perPage = 10, string $pageColumnName = 'page'): self
    {
        $this->hasPagination = true;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->pageColumnName = $pageColumnName;

        return $this;
    }

    public function modifyQuery(callable $modifyQuery): self
    {
        $modifyQuery($this->query);

        return $this;
    }

    /**
     * @return LengthAwarePaginator|Collection LengthAwarePaginator when pagination was attached, Collection otherwise
     */
    public function get(array $columns = ['*']): LengthAwarePaginator|Collection
    {
        $columns = (new Collection($columns))->map(fn ($column) => $this->parseFullColumnName($column));

        $this->buildOrdering();
        $this->buildSearch();

        return $this->buildPaginationAndGetResult($columns);
    }

    /**
     * Set the default locale on each model in the result collection for translatable models.
     */
    private function processResultCollection(Collection $collection): void
    {
        if ($this->modelHasTranslations) {
            $collection->each(function (Model $model): void {
                if (method_exists($model, 'setLocale')) {
                    $model->setLocale($this->locale);
                }
            });
        }
    }

    /**
     * @return array<string, string|bool>
     */
    private function parseFullColumnName(string $column): array
    {
        if (Str::contains($column, '.')) {
            [$table, $column] = explode('.', $column, 2);
        } else {
            $table = $this->model->getTable();
        }

        $translatable = false;
        if (
            property_exists($this->model, 'translatable')
            && is_array($this->model->translatable)
            && in_array($column, $this->model->translatable, true)
        ) {
            $translatable = true;
        }

        return compact('table', 'column', 'translatable');
    }

    /**
     * @param array<string, string|bool> $column
     */
    private function materializeColumnName(array $column, bool $translated = false): string
    {
        return $column['table'] . '.'
            . $column['column']
            . ($translated ? ($column['translatable'] ? '->' . $this->locale : '') : '');
    }

    /**
     * @return array<string>
     */
    private function materializeColumnNames(Collection $columns, bool $translated = false): array
    {
        return $columns->map(fn ($column) => $this->materializeColumnName($column, $translated))->toArray();
    }

    /**
     * Build order query
     */
    private function buildOrdering(): void
    {
        $orderBy = $this->modelHasTranslations
            ? $this->materializeColumnName(
                $this->parseFullColumnName($this->orderBy),
                true,
            )
            : $this->orderBy;

        $this->query->orderBy($orderBy, $this->orderDirection);
    }

    /**
     * Build search query
     */
    private function buildSearch(): void
    {
        if (count($this->searchIn) === 0) {
            return;
        }

        $search = trim((string) $this->search);
        if ($search === '') {
            return;
        }

        $tokens = new Collection(explode(' ', $search));

        $searchIn = (new Collection($this->searchIn))->map(fn (string $column) => $this->parseFullColumnName($column));

        // FIXME there is an issue, if you pass primary key as the only column to search in, it may not work properly

        $tokens->each(function (string $token) use ($searchIn): void {
            $this->query->where(function (Builder $query) use ($token, $searchIn): void {
                $searchIn->each(function (array $column) use ($token, $query): void {
                    // FIXME try to find out how to customize this default behaviour
                    if (
                        $this->model->getKeyName() === $column['column']
                        && $this->model->getTable() === $column['table']
                    ) {
                        if (is_numeric($token) && $token === strval(intval($token))) {
                            $query->orWhere($this->materializeColumnName($column, true), intval($token));
                        }
                    } else {
                        $this->searchLike($query, $column, $token);
                    }
                });
            });
        });
    }

    private function searchLike(Builder $query, array $column, string $token): void
    {
        // MySQL and SQLite use 'like' (case insensitive), PostgreSQL uses 'ilike'
        $likeOperator = 'like';
        if ($this->databaseManager->connection()->getDriverName() === 'pgsql') {
            $likeOperator = 'ilike';
        }

        $query->orWhere($this->materializeColumnName($column, true), $likeOperator, '%' . $token . '%');
    }

    private function buildPaginationAndGetResult(Collection $columns): LengthAwarePaginator|Collection
    {
        if ($this->hasPagination) {
            $result = $this->query->paginate(
                $this->perPage,
                $this->materializeColumnNames($columns),
                $this->pageColumnName,
                $this->currentPage,
            )->withQueryString();
            $this->processResultCollection($result->getCollection());
        } else {
            $result = $this->query->get($this->materializeColumnNames($columns));
            $this->processResultCollection($result);
        }

        return $result;
    }
}
