<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\CategoryRepository;
use App\Document;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(CategoryRepository $category)
    {
        // 公共视图
        view()->composer('home.*', function ($view) use ($category) {
            // 获取导航栏
            $mainMenu = $category->treeLevel2(config('system.category_id.main_menu'));

            // 页脚
            $categoryFooter = $category->getModel()->find(config('system.category_id.footer'));
            if($categoryFooter) {
                $footer = $categoryFooter->document;
            }

            $view->with([
                'mainMenu' => $mainMenu,
                'footer'   => isset($footer) ? $footer : null,
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
