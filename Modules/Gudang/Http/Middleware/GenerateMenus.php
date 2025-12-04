<?php

namespace Modules\Gudang\Http\Middleware;

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
            $menu->add('<i class="fas fa-warehouse c-sidebar-nav-icon"></i> Gudang', [
                'route' => 'backend.gudang.index',
                'class' => "c-sidebar-nav-item",
            ])
            ->data([
                'order' => 85,
                'activematches' => ['admin/gudang*'],
                'permission' => ['view_gudang'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}
