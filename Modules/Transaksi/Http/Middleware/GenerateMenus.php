<?php

namespace Modules\Transaksi\Http\Middleware;

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
            $menu->add('<i class="fas fa-cash-register c-sidebar-nav-icon"></i> Transaksi', [
                'route' => 'backend.transaksi.index',
                'class' => "c-sidebar-nav-item",
            ])
            ->data([
                'order' => 86,
                'activematches' => ['admin/transaksi*'],
                'permission' => ['view_transaksi'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}
