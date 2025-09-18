<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->configureModules();
        $this->configureVite();
    }

    /**
     * @return void
     */
    private function configureModules(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
    }

    /**
     * @return void
     */
    private function configureVite(): void
    {
        Vite::usePrefetchStrategy('aggressive');
    }
}
