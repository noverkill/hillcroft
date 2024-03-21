<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bindMethod(ProcessXmlFileJob::class.'@handle', function ($job, $app) {
            return $job->handle(
                $app->make(App\Models\Product::class),
                $app->make(XMLReader::class),
                $app->make(Illuminate\Support\Facades\DB::class),
                $app->make(Illuminate\Support\Facades\Log::class),
                $app->make(Illuminate\Support\Facades\Mail::class),
                $app->make(App\Mail\ProductsImported::class, ['products' => []])
            );
        });
    }
}
