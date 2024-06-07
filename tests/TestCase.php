<?php

namespace Asantibanez\LaravelEloquentStateMachines\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Javoscript\MacroableModels\MacroableModelsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Asantibanez\LaravelEloquentStateMachines\LaravelEloquentStateMachinesServiceProvider;

class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // 加载工厂
        $this->withFactories(__DIR__.'/../database/factories');
        $this->withFactories(__DIR__.'/database/factories');

        // 运行迁移：使用 migrate:fresh 删除表并重新创建
        $this->artisan('migrate:fresh', ['--database' => 'testing'])->run();

        // 运行迁移
        $this->runDatabaseMigrations();
    }

    protected function getPackageProviders($app)
    {
        return [
            MacroableModelsServiceProvider::class,
            LaravelEloquentStateMachinesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // 设置测试环境的数据库连接
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'test_db'),
            'username' => env('DB_USERNAME', 'trustanchor'),
            'password' => env('DB_PASSWORD', 'Yai3hahMaepi9uyo3Joh'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);

        // 包含迁移文件
        include_once __DIR__ . '/../database/migrations/create_state_histories_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_pending_transitions_table.php.stub';
        include_once __DIR__ . '/database/migrations/create_sales_orders_table.php';
        include_once __DIR__ . '/database/migrations/create_sales_managers_table.php';
    }

    protected function runDatabaseMigrations()
    {
        // 运行迁移
        (new \CreateStateHistoriesTable())->up();
        (new \CreatePendingTransitionsTable())->up();
        (new \CreateSalesOrdersTable())->up();
        (new \CreateSalesManagersTable())->up();
    }
}

