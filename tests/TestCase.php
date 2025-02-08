<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Tests;

use Brackets\AdminListing\AdminListing;
use Brackets\Translatable\Models\WithTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase as Test;
use function assert;

abstract class TestCase extends Test
{
    protected Model $testModel;

    protected AdminListing $listing;

    protected AdminListing $translatedListing;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
        $this->listing = AdminListing::create(TestModel::class);
        $this->translatedListing = AdminListing::create(TestTranslatableModel::class);
    }

    /**
     * @param Application $app
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('admin-listing.with-translations-class', WithTranslations::class);
        if (env('DB_CONNECTION') === 'pgsql') {
            $app['config']->set('database.default', 'pgsql');
            $app['config']->set('database.connections.pgsql', [
                'driver' => 'pgsql',
                'host' => 'pgsql',
                'port' => '5432',
                'database' => env('DB_DATABASE', 'laravel'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', 'bestsecret'),
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
                'sslmode' => 'prefer',
            ]);
        } else if (env('DB_CONNECTION') === 'mysql') {
            $app['config']->set('database.default', 'mysql');
            $app['config']->set('database.connections.mysql', [
                'driver' => 'mysql',
                'host' => 'mysql',
                'port' => '3306',
                'database' => env('DB_DATABASE', 'laravel'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', 'bestsecret'),
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
                'sslmode' => 'prefer',
            ]);
        } else {
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        }
    }

    protected function setUpDatabase(Application $app): void
    {
        $schema = $app['db']->connection()->getSchemaBuilder();
        assert($schema instanceof Builder);
        $schema->dropIfExists('test_models');
        $schema->create('test_models', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('color');
            $table->integer('number');
            $table->dateTime('published_at');
        });

        TestModel::create([
            'name' => 'Alpha',
            'color' => 'red',
            'number' => 999,
            'published_at' => '2000-06-01 00:00:00',
        ]);

        (new Collection(range(2, 10)))->each(static function ($i): void {
            TestModel::create([
                'name' => 'Zeta ' . $i,
                'color' => 'yellow',
                'number' => $i,
                'published_at' => (1998 + $i) . '-01-01 00:00:00',
            ]);
        });

        $schema->dropIfExists('test_translatable_models');
        $schema->create('test_translatable_models', static function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('number');
            $table->dateTime('published_at');
            $table->jsonb('name')->nullable();
            $table->jsonb('color')->nullable();
        });

        TestTranslatableModel::create([
            'name' => [
                'en' => 'Alpha',
                'sk' => 'Alfa',
            ],
            'color' => [
                'en' => 'red',
                'sk' => 'cervena',
            ],
            'number' => 999,
            'published_at' => '2000-06-01 00:00:00',
        ]);

        (new Collection(range(2, 10)))->each(static function ($i): void {
            TestTranslatableModel::create([
                'name' => [
                    'en' => 'Zeta ' . $i,
                ],
                'color' => [
                    'en' => 'yellow',
                ],
                'number' => $i,
                'published_at' => (1998 + $i) . '-01-01 00:00:00',
            ]);
        });
    }
}
