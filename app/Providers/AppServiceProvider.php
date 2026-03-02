<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View; // 👈 LÍNEA NUEVA (1)
use App\View\Composers\VendedorSidebarComposer; // 👈 LÍNEA NUEVA (2)

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
        $this->configureDefaults();
        
        // 👇 CÓDIGO NUEVO - View Composers (AGREGA ESTO)
        View::composer([
            'vendedor.layouts.app',
            'vendedor.dashboard.*',
            'vendedor.pedidos.*',
            'vendedor.ventas.*',
            'vendedor.clientes.*',
            'vendedor.catalogo.*'
        ], VendedorSidebarComposer::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}