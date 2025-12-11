<?php

namespace App\Providers;

use App\Models\Sop;
use App\Policies\SopPolicy;
use App\Observers\SopObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView; // Import ini
use Illuminate\Support\Facades\Blade;      // Import ini
use Filament\View\PanelsRenderHook;        // Import ini


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
        Gate::policy(Sop::class, SopPolicy::class);
        Sop::observe(SopObserver::class);
        // Inject Livewire Component ke Header Global Search (Posisi strategis di kanan atas)
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE, 
            fn (): string => Blade::render('@livewire("notification-bell")')
        );
    }
}
