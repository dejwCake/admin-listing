# Upgrade Guide: v1 to v2

## Requirements

- PHP `^8.5` (was `^8.2`)
- Laravel 12 (illuminate/support `^12.0`)
- PHPUnit `^13.0` (was `^11.x`, dev only)
- `dejwcake/craftable-translatable` `^2.0` (was `^1.0`, dev only)

## Breaking Changes

### Facade Removed

The `Brackets\AdminListing\Facades\AdminListing` facade has been removed. Use dependency injection with `AdminListingBuilder` instead.

**Before (v1):**
```php
use Brackets\AdminListing\Facades\AdminListing;
// or via alias:
use AdminListing;

$listing = AdminListing::create(Post::class);
```

**After (v2):**
```php
use Brackets\AdminListing\Services\AdminListingBuilder;

public function __construct(private readonly AdminListingBuilder $adminListingBuilder)
{
}

public function index(Request $request)
{
    $listing = $this->adminListingBuilder->for(Post::class)->build();
}
```

### Static `AdminListingService::create()` Removed

The static factory method `AdminListingService::create(string $modelName)` has been removed. Use `AdminListingBuilder::for()->build()` instead (see above).

### `AdminListingService::setModel()` Removed

The `setModel()` method has been removed. Model is now set via `AdminListingBuilder::for()`.

### `AdminListingService` Constructor Changed

`AdminListingService` constructor is no longer private. It now accepts explicit dependencies via DI. However, you should not instantiate it directly — use `AdminListingBuilder` instead.

### Class Visibility Changes

- `AdminListingService` is now `final` — it cannot be extended
- All `protected` properties and methods on `AdminListingService` are now `private`
- If you were extending `AdminListingService`, you need to use composition instead

### Exception Changes

- `setLocale()` on a non-translatable model now throws `ModelNotTranslatableException` (new, extends `LogicException`) instead of a generic `Exception`
- `NotAModelClassException` now extends `LogicException` instead of `Exception`

### Service Provider Changes

- `AdminListingServiceProvider` no longer implements `DeferrableProvider`
- The `admin-listing` container binding and `AdminListing` alias are removed
- `AdminListingBuilder` is registered as a singleton instead

### Config File Location

Config file moved from `install-stubs/config/admin-listing.php` to `config/admin-listing.php`. If you have already published the config, no action is needed.

### Install Command

`AdminListingInstall` now uses DI for `Application` instead of the `base_path()` helper.

## New Features

### `AdminListing` Contract

A new `Brackets\AdminListing\Contracts\AdminListing` interface defines the public API. You can type-hint against this interface instead of the concrete `AdminListingService`.

### `AdminListingBuilder`

New builder class providing an immutable API for creating listing instances:

```php
$listing = $adminListingBuilder->for(Post::class)->build();
```

The `for()` method returns a clone, so the builder can be safely reused.

### `ModelNotTranslatableException`

New dedicated exception for translatable-related errors, replacing generic `Exception`.

## Migration Checklist

1. Replace all `AdminListing::create(Model::class)` calls with `AdminListingBuilder::for(Model::class)->build()`
2. Replace facade imports with `AdminListingBuilder` DI
3. Remove any `AdminListing` facade alias references
4. Update exception catches: `Exception` to `ModelNotTranslatableException` where catching `setLocale()` errors
5. If extending `AdminListingService`, refactor to use composition
6. Update PHP to `^8.5`
