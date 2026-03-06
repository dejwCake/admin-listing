<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Services;

use Brackets\AdminListing\Contracts\AdminListing;
use Brackets\AdminListing\Exceptions\NotAModelClassException;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Throwable;

final class AdminListingBuilder
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
     * Build the AdminListingService instance
     *
     * @throws NotAModelClassException
     */
    public function build(): AdminListing
    {
        if ($this->model === null) {
            throw new NotAModelClassException('Model must be set before building AdminListing');
        }

        $model = $this->model;

        if (is_string($model)) {
            try {
                $model = $this->app->make($model);
            } catch (Throwable) {
                throw new NotAModelClassException('AdminListing works only with Eloquent Models');
            }
        }

        if (!$model instanceof Model) {
            throw new NotAModelClassException('AdminListing works only with Eloquent Models');
        }

        /** @var class-string|null $withTranslationsClass */
        $withTranslationsClass = $this->config->get('admin-listing.with-translations-class');

        $modelHasTranslations = $withTranslationsClass !== null
            && $model instanceof $withTranslationsClass;

        return new AdminListingService(
            $this->databaseManager,
            $model,
            $modelHasTranslations,
            $this->app->getLocale(),
        );
    }
}
