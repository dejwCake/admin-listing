<?php

namespace Brackets\AdminListing\Tests;

use Brackets\AdminListing\AdminListing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Test;

abstract class TestCase extends Test
{

    /**
     * @var Model
     */
    protected Model $testModel;

    /**
     * @var AdminListing
     */
    protected AdminListing $listing;

    /**
     * @var AdminListing
     */
    protected AdminListing $translatedListing;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
        $this->listing = AdminListing::create(TestModel::class);
        $this->translatedListing = AdminListing::create(TestTranslatableModel::class);
    }

    protected function getEnvironmentSetUp($app): void
    {
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

    /**
     * @param Application $app
     */
    protected function setUpDatabase(Application $app): void
    {
        /** @var Builder $schema */
        $schema = $app['db']->connection()->getSchemaBuilder();
        $schema->dropIfExists('test_models');
        $schema->create('test_models', function (Blueprint $table) {
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

        collect(range(2, 10))->each(function ($i) {
            TestModel::create([
                'name' => 'Zeta ' . $i,
                'color' => 'yellow',
                'number' => $i,
                'published_at' => (1998 + $i) . '-01-01 00:00:00',
            ]);
        });

        $schema->dropIfExists('test_translatable_models');
        $schema->create('test_translatable_models', function (Blueprint $table) {
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

        collect(range(2, 10))->each(function ($i) {
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
