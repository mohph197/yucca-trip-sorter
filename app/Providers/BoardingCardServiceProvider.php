<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BoardingCardRepository;
use App\Services\BoardingCardSorter;
use Illuminate\Contracts\Foundation\Application;

class BoardingCardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BoardingCardRepository::class, function (Application $app) {
            return new BoardingCardRepository();
        });

        $this->app->bind(BoardingCardSorter::class, function (Application $app) {
            return new BoardingCardSorter(
                $app->make(BoardingCardRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
