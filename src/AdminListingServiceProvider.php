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
            $this->publish();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin-listing.php', 'admin-listing');

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

    private function publish(): void
    {
        $this->publishes([
            __DIR__ . '/../config/admin-listing.php' => $this->app->configPath('admin-listing.php'),
        ], 'config');
    }
}
