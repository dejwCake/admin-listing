<?php

declare(strict_types=1);

namespace Brackets\AdminListing;

use Brackets\AdminListing\Builders\ListingBuilder;
use Brackets\AdminListing\Console\Commands\AdminListingInstall;
use Illuminate\Support\ServiceProvider;
use Override;

class AdminListingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publish();
        }
    }

    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin-listing.php', 'admin-listing');

        $this->app->singleton(ListingBuilder::class);

        $this->commands([
            AdminListingInstall::class,
        ]);
    }

    private function publish(): void
    {
        $this->publishes([
            __DIR__ . '/../config/admin-listing.php' => $this->app->configPath('admin-listing.php'),
        ], 'config');
    }
}
