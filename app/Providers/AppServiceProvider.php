<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\AcademicYear;

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
        // Make the global academic-year selector available on every authenticated page.
        View::composer('layouts.app', function ($view) {
            $years = AcademicYear::orderByDesc('start_date')->get();
            $view->with('globalAcademicYears', $years);
            $view->with('globalActiveYearId', AcademicYear::currentId());
        });
    }
}
