<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\ProductRepositoryInterface::class,
            \App\Repositories\ProductRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CurrencyRepositoryInterface::class,
            \App\Repositories\CurrencyRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ProductPriceRepositoryInterface::class,
            \App\Repositories\ProductPriceRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
