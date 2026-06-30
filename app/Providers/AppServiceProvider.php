<?php

namespace App\Providers;

use App\Events\PaymentCompleted;
use App\Listeners\HandlePaymentCompleted;
use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $smtpPassword = (string) config('mail.mailers.smtp.password');

        if (
            config('mail.default') === 'smtp'
            && (
                empty(config('mail.mailers.smtp.username'))
                || empty($smtpPassword)
                || str_contains($smtpPassword, 'YOUR_API')
                || str_contains($smtpPassword, '<')
            )
        ) {
            config(['mail.default' => 'log']);
        }

        Paginator::useBootstrapFive();

        Event::listen(PaymentCompleted::class, HandlePaymentCompleted::class);

        View::composer(['partials.navbar', 'partials.category-bar', 'layouts.app', 'home'], function ($view) {
            try {
                $view->with('navCategories', Category::orderBy('name')->get());
            } catch (\Throwable) {
                $view->with('navCategories', collect());
            }
        });
    }
}
