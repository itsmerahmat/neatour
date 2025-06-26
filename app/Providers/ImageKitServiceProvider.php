<?php

namespace App\Providers;

use ImageKit\ImageKit;
use App\Services\ImageKitService;
use Illuminate\Support\ServiceProvider;

class ImageKitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageKit::class, function ($app) {
            return new ImageKit(
                config('imagekit.public_key'),
                config('imagekit.private_key'),
                config('imagekit.url_endpoint')
            );
        });

        $this->app->singleton(ImageKitService::class, function ($app) {
            return new ImageKitService($app->make(ImageKit::class));
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
