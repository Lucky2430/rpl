<?php

namespace Modules\Barang\Http\Middleware;

use Closure;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         *
         * Module Menu for Admin Backend
         *
         * *********************************************************************
         */
        \Menu::make('admin_sidebar', function ($menu) {

            // Tags
            $menu->add('<i class="fas fa-box c-sidebar-nav-icon"></i> Barang', [
                'route' => 'backend.barang.index',
                'class' => "c-sidebar-nav-item",
            ])
            ->data([
                'order' => 86,
                'activematches' => ['admin/barang*'],
                'permission' => ['view_barang'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}
