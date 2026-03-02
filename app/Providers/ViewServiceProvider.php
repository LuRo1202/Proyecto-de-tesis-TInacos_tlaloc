<?php
// app/Providers/ViewServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\CarritoHelper;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compartir cartCount con TODAS las vistas
        View::composer('*', function ($view) {
            $view->with('cartCount', CarritoHelper::getCartCount());
        });
    }
}