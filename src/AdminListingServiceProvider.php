<?php

namespace Brackets\AdminListing;

use Brackets\AdminListing\Console\Commands\AdminListingInstall;
use Brackets\AdminListing\Facades\AdminListing as AdminListingFacade;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AdminListingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->commands([
            AdminListingInstall::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../install-stubs/config/admin-listing.php' => config_path('admin-listing.php'),
            ], 'config');
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../install-stubs/config/admin-listing.php', 'admin-listing');
        $this->app->bind('admin-listing', AdminListing::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('AdminListing', AdminListingFacade::class);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['admin-listing'];
    }
}
