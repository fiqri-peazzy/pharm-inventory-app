<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Map all morph type aliases globally so stock_cards.reference_type works correctly
        Relation::morphMap([
            'receiving'     => \App\Models\Receiving::class,
            'initial_usage' => \App\Models\Receiving::class, // Legacy alias
        ]);
    }
}
