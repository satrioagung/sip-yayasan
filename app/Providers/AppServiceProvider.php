<?php

namespace App\Providers;

use App\Models\Institution;
use App\Policies\InstitutionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Daftarkan Policy
        Gate::policy(Institution::class, InstitutionPolicy::class);

        // Lokalisasi Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');

        // Pagination menggunakan Tailwind CSS
        \Illuminate\Pagination\Paginator::useTailwind();
    }
}
