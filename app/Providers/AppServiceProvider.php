<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Include helper manual jika composer dump-autoload tidak bisa
        $helperPath = app_path('Helpers/EmbedHelper.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }
}
