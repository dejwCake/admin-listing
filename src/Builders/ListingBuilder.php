<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Builders;

use Brackets\AdminListing\Contracts\Listing;
use Brackets\AdminListing\Exceptions\NotAModelClassException;
use Brackets\AdminListing\Services\ListingService;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Throwable;

final class ListingBuilder
{
    private Model|string|null $model = null;

    public function __construct(
        private readonly DatabaseManager $databaseManager,
        private readonly Config $config,
        private readonly Application $app,
    ) {
    }

    /**
     * Set the model to build listing for
     */
    public function for(Model|string $model): self
    {
        $clone = clone $this;
        $clone->model = $model;

        return $clone;
    }

    /**
     * Build the ListingService instance
     *
     * @throws NotAModelClassException
     */
    public function build(): Listing
    {
        if ($this->model === null) {
            throw new NotAModelClassException('Model must be set before building Listing');
        }

        $model = $this->model;

        if (is_string($model)) {
            try {
                $model = $this->app->make($model);
            } catch (Throwable) {
                throw new NotAModelClassException('Listing works only with Eloquent Models');
            }
        }

        if (!$model instanceof Model) {
            throw new NotAModelClassException('Listing works only with Eloquent Models');
        }

        /** @var class-string|null $withTranslationsClass */
        $withTranslationsClass = $this->config->get('admin-listing.with-translations-class');

        $modelHasTranslations = $withTranslationsClass !== null
            && $model instanceof $withTranslationsClass;

        return new ListingService(
            $this->databaseManager,
            $model,
            $modelHasTranslations,
            $this->app->getLocale(),
        );
    }
}
