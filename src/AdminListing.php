<?php

namespace Brackets\AdminListing;

use Brackets\AdminListing\Exceptions\NotAModelClassException;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AdminListing
{
    protected Model $model;

    protected Builder $query;

    protected int $currentPage;

    protected int $perPage;

    protected string $pageColumnName = 'page';

    protected bool $hasPagination = false;

    protected bool $modelHasTranslations = false;

    protected string $locale;

    protected string $orderBy;

    protected string $orderDirection = 'asc';

    protected string $search;

    /** @var array<string> */
    protected array $searchIn = [];

    public static function create(string $modelName): self
    {
        return (new static)->setModel($modelName);
    }

    /**
     * Set model admin listing works with
     *
     * Setting the model is required
     *
     * @throws NotAModelClassException
     */
    public function setModel(Model|string $model): self
    {
        if (is_string($model)) {
            try {
                $model = app($model);
            } catch (Throwable) {
                throw new NotAModelClassException("AdminListing works only with Eloquent Models");
            }
        }

        if (!is_a($model, Model::class)) {
            throw new NotAModelClassException("AdminListing works only with Eloquent Models");
        }

        $this->model = $model;

        $this->init();

        return $this;
    }

    /**
     * Init properties
     */
    private function init(): void
    {
        $withTranslationsClassName = config('admin-listing.with-translations-class');
        if ($this->model instanceof $withTranslationsClassName) {
            $this->modelHasTranslations = true;
            $this->locale = $this->model->locale ?: app()->getLocale();
        }

        $this->query = $this->model->newQuery();

        $this->orderBy = $this->model->getKeyName();
    }

    /**
     * Process request and get data
     *
     * You should always specify an array of columns that are about to be queried
     *
     * You can specify columns which should be searched
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
     * @param array $searchIn array of columns which should be searched in (only text, character varying or primary key are allowed)
     * @throws Exception
     * @return LengthAwarePaginator|Collection The result is either LengthAwarePaginator (when pagination was attached) or simple Collection otherwise
     */
    public function processRequestAndGet(Request $request, array $columns = ['*'], ?array $searchIn = null, ?callable $modifyQuery = null, ?string $locale = null): LengthAwarePaginator|Collection
    {
        // process all the basic stuff
        $this->attachOrdering(
            $request->input('orderBy', $this->model->getKeyName()),
            $request->input('orderDirection', 'asc')
        )->attachSearch(
            $request->input('search', null),
            $searchIn
        );

        // we want to attach pagination if bulk filter is disabled otherwise we want to select all data without pagination
        if (!$request->input('bulk')) {
            $this->attachPagination(
                (int) $request->input('page', 1),
                (int) $request->input('per_page', $request->cookie('per_page', 10))
            );
        }
        // add custom modifications
        if ($modifyQuery !== null) {
            $this->modifyQuery($modifyQuery);
        }

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        // if bulk filter is enabled we want to get only primary keys
        if ($request->input('bulk')) {
            return $this->get(['id']);
        }

        // execute query and get the results
        return $this->get($columns);
    }

    /**
     * Set the locale you want to query
     *
     * This method is only valid for Translatable models
     *
     * @throws Exception
     */
    public function setLocale(string $locale): self
    {
        if (!$this->modelHasTranslations()) {
            throw new Exception('Model is not translatable, so you cannot set locale');
        }
        $this->locale = $locale;
        return $this;
    }

    /**
     * Attach the ordering functionality
     *
     * Any repeated call to this method is going to have no effect and original ordering is going to be used.
     * This is due to the limitation of the Illuminate\Database\Eloquent\Builder.
     */
    public function attachOrdering(string $orderBy, string $orderDirection = 'asc'): self
    {
        $this->orderBy = $orderBy;
        $this->orderDirection = $orderDirection;
        return $this;
    }

    /**
     * Build order query
     */
    private function buildOrdering(): void
    {
        if ($this->modelHasTranslations()) {
            $orderBy = $this->materializeColumnName($this->parseFullColumnName($this->orderBy), true);
        } else {
            $orderBy = $this->orderBy;
        }

        $this->query->orderBy($orderBy, $this->orderDirection);
    }


    /**
     * Attach the searching functionality
     *
     * @param string $search searched string
     * @param array<string> $searchIn array of columns which should be searched in (only text, character varying or primary key are allowed)
     * @return $this
     */
    public function attachSearch(string $search, array $searchIn): self
    {
        $this->search = $search;
        $this->searchIn = $searchIn;
        return $this;
    }

    /**
     * Build search query
     */
    private function buildSearch(): void
    {
        if (count($this->searchIn) === 0) {
            return ;
        }

        // if empty string, then we don't search at all
        $search = trim($this->search);
        if ($search === '') {
            return ;
        }

        $tokens = new Collection(explode(' ', $search));

        $searchIn = (new Collection($this->searchIn))->map(function (string $column) {
            return $this->parseFullColumnName($column);
        });

        // FIXME there is an issue, if you pass primary key as the only column to search in, it may not work properly

        $tokens->each(function (string $token) use ($searchIn) {
            $this->query->where(function (Builder $query) use ($token, $searchIn) {
                $searchIn->each(function (array $column) use ($token, $query) {
                    // FIXME try to find out how to customize this default behaviour
                    if ($this->model->getKeyName() === $column['column'] && $this->model->getTable() === $column['table']) {
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

        // MySQL and SQLite uses 'like' pattern matching operator that is case insensitive
        $likeOperator = 'like';

        // but PostgreSQL uses 'ilike' pattern matching operator for this same functionality
        if (DB::connection()->getDriverName() == 'pgsql') {
            $likeOperator = 'ilike';
        }

        $query->orWhere($this->materializeColumnName($column, true), $likeOperator, '%'.$token.'%');
    }

    /**
     * Attach the pagination functionality
     */
    public function attachPagination(int $currentPage, int $perPage = 10, string $pageColumnName = 'page'): self
    {
        $this->hasPagination = true;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->pageColumnName = $pageColumnName;

        return $this;
    }

    /**
     * Modify built query in any way
     */
    public function modifyQuery(callable $modifyQuery): self
    {
        $modifyQuery($this->query);

        return $this;
    }

    /**
     * Execute query and get data
     *
     * @param array<string> $columns
     * @return LengthAwarePaginator|Collection The result is either LengthAwarePaginator (when pagination was attached) or simple Collection otherwise
     */
    public function get(array $columns = ['*'])
    {
        $columns = (new Collection($columns))->map(function ($column) {
            return $this->parseFullColumnName($column);
        });

        $this->buildOrdering();
        $this->buildSearch();

        return $this->buildPaginationAndGetResult($columns);
    }

    private function buildPaginationAndGetResult(Collection $columns): LengthAwarePaginator|Collection
    {
        if ($this->hasPagination) {
            $result = $this->query->paginate(
                $this->perPage,
                $this->materializeColumnNames($columns),
                $this->pageColumnName,
                $this->currentPage
            )->withQueryString();
            $this->processResultCollection($result->getCollection());
        } else {
            $result = $this->query->get($this->materializeColumnNames($columns));
            $this->processResultCollection($result);
        }

        return $result;
    }

    /**
     * @param Collection $collection
     */
    protected function processResultCollection(Collection $collection): void
    {
        if ($this->modelHasTranslations()) {
            // we need to set this default locale ad hoc
            $collection->each(function (Model $model) {
                $model->setLocale($this->locale);
            });
        }
    }

    /**
     * @return array<string, string|bool>
     */
    protected function parseFullColumnName(string $column): array
    {
        if (Str::contains($column, '.')) {
            list($table, $column) = explode('.', $column, 2);
        } else {
            $table = $this->model->getTable();
        }

        $translatable = false;
        if (is_array($this->model->translatable) && in_array($column, $this->model->translatable)) {
            $translatable = true;
        }

        return compact('table', 'column', 'translatable');
    }

    /**
     * @param array<string, string|bool> $column
     */
    protected function materializeColumnName(array $column, bool $translated = false): string
    {
        return $column['table'].'.'.$column['column'].($translated ? ($column['translatable'] ? '->'.$this->locale : '') : '');
    }

    /**
     * @return bool
     */
    protected function modelHasTranslations(): bool
    {
        return $this->modelHasTranslations;
    }

    /**
     * @return array<string>
     */
    protected function materializeColumnNames(Collection $columns, bool $translated = false): array
    {
        return $columns->map(function ($column) use ($translated) {
            return $this->materializeColumnName($column, $translated);
        })->toArray();
    }
}
