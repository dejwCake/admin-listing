<?php

declare(strict_types=1);

namespace Brackets\AdminListing;

use Brackets\AdminListing\Console\Commands\AdminListingInstall;
use Brackets\AdminListing\Facades\AdminListing;
use Brackets\AdminListing\Services\AdminListingService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AdminListingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../install-stubs/config/admin-listing.php' => config_path('admin-listing.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../install-stubs/config/admin-listing.php', 'admin-listing');

        $this->app->bind('admin-listing', AdminListingService::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('AdminListing', AdminListing::class);

        $this->commands([
            AdminListingInstall::class,
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['admin-listing'];
    }
}
