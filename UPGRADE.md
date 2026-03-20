# Upgrade Guide: v1 to v2

## Requirements

- PHP `^8.5` (was `^8.2`)
- Laravel 13 (illuminate/support `^13.0`)
- PHPUnit `^13.0` (was `^11.x`, dev only)
- `dejwcake/craftable-translatable` `^2.0` (was `^1.0`, dev only)

## Breaking Changes

### Facade Removed

The `Brackets\AdminListing\Facades\AdminListing` facade has been removed. Use dependency injection with `ListingBuilder` instead.

**Before (v1):**
```php
use Brackets\AdminListing\Facades\AdminListing;
// or via alias:
use AdminListing;

$listing = AdminListing::create(Post::class);
```

**After (v2):**
```php
use Brackets\AdminListing\Builders\ListingBuilder;
use Brackets\AdminListing\Builders\ListingQueryBuilder;

public function __construct(
    private readonly ListingBuilder $listingBuilder,
    private readonly ListingQueryBuilder $listingQueryBuilder,
)
{
}

public function index(Request $request)
{
    $data = $this->listingBuilder->for(Post::class)->build()
        ->processRequestAndGet(
            $this->listingQueryBuilder->fromRequest(
                $request,
                ['id', 'title', 'name'],
                ['id', 'title', 'name'],
            ),
        );
}
```

### Classes Renamed and Reorganized

| Old FQCN | New FQCN |
|---|---|
| `Brackets\AdminListing\Contracts\AdminListing` | `Brackets\AdminListing\Contracts\Listing` |
| `Brackets\AdminListing\Services\AdminListingBuilder` | `Brackets\AdminListing\Builders\ListingBuilder` |
| `Brackets\AdminListing\Services\AdminListingService` | `Brackets\AdminListing\Services\ListingService` |

New classes:
- `Brackets\AdminListing\Dtos\ListingQuery` — value object encapsulating listing query parameters
- `Brackets\AdminListing\Builders\ListingQueryBuilder` — builds `ListingQuery` from HTTP request

### `processRequestAndGet()` Signature Changed

The method no longer accepts `Request` directly. Use `ListingQuery` value object instead.

**Before (v1/early v2):**
```php
$listing->processRequestAndGet(
    $request,
    ['id', 'name'],
    ['id', 'name'],
    function (Builder $query) { ... },
    'sk',
);
```

**After (v2):**
```php
$listing->processRequestAndGet(
    $this->listingQueryBuilder->fromRequest(
        $request,
        ['id', 'name'],
        ['id', 'name'],
    ),
    function (Builder $query) { ... },
    'sk',
);
```

Or construct `ListingQuery` directly without a request:
```php
use Brackets\AdminListing\Dtos\ListingQuery;

$listing->processRequestAndGet(
    new ListingQuery(
        columns: ['id', 'name'],
        searchIn: ['id', 'name'],
        orderBy: 'name',
        orderDirection: 'desc',
        search: 'term',
        page: 1,
        perPage: 25,
    ),
);
```

### Static `AdminListingService::create()` Removed

The static factory method `AdminListingService::create(string $modelName)` has been removed. Use `ListingBuilder::for()->build()` instead (see above).

### `AdminListingService::setModel()` Removed

The `setModel()` method has been removed. Model is now set via `ListingBuilder::for()`.

### `ListingService` Constructor Changed

`ListingService` (formerly `AdminListingService`) constructor is no longer private. It now accepts explicit dependencies via DI. However, you should not instantiate it directly — use `ListingBuilder` instead.

### Class Visibility Changes

- `ListingService` is now `final` — it cannot be extended
- All `protected` properties and methods on `ListingService` are now `private`
- If you were extending `AdminListingService`, you need to use composition instead

### Exception Changes

- `setLocale()` on a non-translatable model now throws `ModelNotTranslatableException` (new, extends `LogicException`) instead of a generic `Exception`
- `NotAModelClassException` now extends `LogicException` instead of `Exception`

### Service Provider Changes

- `AdminListingServiceProvider` no longer implements `DeferrableProvider`
- The `admin-listing` container binding and `AdminListing` alias are removed
- `ListingBuilder` is registered as a singleton instead

### Config File Location

Config file moved from `install-stubs/config/admin-listing.php` to `config/admin-listing.php`. If you have already published the config, no action is needed.

### Install Command

`AdminListingInstall` now uses DI for `Application` instead of the `base_path()` helper.

## New Features

### `Listing` Contract

A new `Brackets\AdminListing\Contracts\Listing` interface defines the public API. You can type-hint against this interface instead of the concrete `ListingService`.

### `ListingBuilder`

Builder class providing an immutable API for creating listing instances:

```php
$listing = $listingBuilder->for(Post::class)->build();
```

The `for()` method returns a clone, so the builder can be safely reused.

### `ListingQuery` Value Object

A `final readonly` value object that encapsulates all listing query parameters (`columns`, `searchIn`, `orderBy`, `orderDirection`, `search`, `page`, `perPage`, `bulk`), decoupling the listing service from the HTTP request.

### `ListingQueryBuilder`

A builder that constructs `ListingQuery` from an HTTP `Request`, extracting the standard query parameters.

### `ModelNotTranslatableException`

New dedicated exception for translatable-related errors, replacing generic `Exception`.

## Migration Checklist

1. Replace all `AdminListing::create(Model::class)` calls with `ListingBuilder::for(Model::class)->build()`
2. Replace facade imports with `ListingBuilder` and `ListingQueryBuilder` DI
3. Remove any `AdminListing` facade alias references
4. Update `processRequestAndGet()` calls: wrap request params in `ListingQueryBuilder::fromRequest()` or construct `ListingQuery` directly
5. Rename imports: `AdminListingBuilder` → `ListingBuilder`, `AdminListingService` → `ListingService`, `Contracts\AdminListing` → `Contracts\Listing`
6. Update exception catches: `Exception` to `ModelNotTranslatableException` where catching `setLocale()` errors
7. If extending `AdminListingService`, refactor to use composition
8. Update PHP to `^8.5`
