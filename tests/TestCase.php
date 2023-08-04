<?php

namespace Phpsa\LaravelCurrencies\Tests;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Route;
use Phpsa\LaravelCurrencies\Amount;
use Phpsa\LaravelCurrencies\AmountCast;
use Phpsa\LaravelCurrencies\CurrenciesServiceProvider;
use Phpsa\LaravelCurrencies\Tests\Stubs\Product;
use Phpsa\LaravelCurrencies\Tests\Stubs\ProductController;

class TestCase extends \Orchestra\Testbench\TestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        Route::post('products', ProductController::class);

        Amount::setBaseCurrency(TestCurrency::fromCode('EUR'));
        AmountCast::defaultStoredAs('%s');
        Product::$testCast = [];
        ProductController::$rules = [];
    }

    protected function getPackageProviders($app)
    {
        $app->afterResolving(
            'migrator',
            function (Migrator $migrator) {
                $migrator->path(__DIR__.'/migrations/');
            }
        );
        return [CurrenciesServiceProvider::class];
    }

    /**
     * @param  $amount
     * @param  null $currency
     * @return Amount
     *
     * @throws \Exception
     */
    protected function amount($amount, $currency = null)
    {
        return new Amount($amount, $currency);
    }

    /**
     * @param  $abstract
     * @return $this
     */
    protected function unsetContainer($abstract)
    {
        app()->bind(
            $abstract,
            function () {
            }
        );

        return $this;
    }
}
