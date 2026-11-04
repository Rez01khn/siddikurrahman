<?php

namespace App\Providers;

use App\Models\WebsiteSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view): void {
            $request = request();
            if (! $request->attributes->has('website_settings_loaded')) {
                $request->attributes->set('website_settings_loaded', true);
                $request->attributes->set('website_settings', Schema::hasTable('website_settings') ? WebsiteSetting::find(1) : null);
            }
            $view->with('siteSettings', $request->attributes->get('website_settings'));
        });
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinutes(10, 5)->by('contact:'.$request->ip()));
        RateLimiter::for('admin-login', function (Request $request): array {
            $email = $request->input('email');
            $email = is_string($email) ? mb_strtolower(trim($email)) : '';

            return [
                Limit::perMinute(20)->by('admin-ip:'.$request->ip()),
                Limit::perMinute(5)->by('admin-account:'.hash('sha256',
                    $email.'|'.$request->ip()
                )),
            ];
        });
    }
}
