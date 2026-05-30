<?php

namespace App\Providers;

use App\Helpers\ProgressNotificationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layout.partials.header', function ($view) {
            $view->with('progressNotifications', ProgressNotificationService::getForCurrentUser());
        });
    }
}
