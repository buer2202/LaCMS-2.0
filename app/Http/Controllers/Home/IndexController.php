<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Category;
use App\Document;

class IndexController extends Controller
{
    public function index()
    {
        // seo
        $seo = [
            'title'       => config('app.name'),
            'keywords'    => config('app.name'),
            'description' => config('app.name'),
        ];

        // 菜单高亮
        $menuRootId = true;

        // 图片轮播
        $DocSlideShow = Document::where('category_id', config('system.category_id.index.group.slide_show'))->where('status', 1)->first();
        if($DocSlideShow) {
            $slideShow = $DocSlideShow->attachment()->orderBy('updated_at', 'desc')->get();
        }

        // 快捷导航
        $quickNav = Category::where('parent_id', config('system.category_id.index.group.quick_nav'))->where('status', 1)->get();

        // 解决方案
        $solutions = Category::find(config('system.category_id.index.group.solutions'));

        // 产品
        $products = Category::find(config('system.category_id.index.group.products'));

        // 服务
        $services = Category::find(config('system.category_id.index.group.services'));

        // 技术支持
        $support = Category::find(config('system.category_id.index.group.support'));

        // 关于我们
        $aboutUs = Category::find(config('system.category_id.index.group.about_us'));

        return view('home.index.index', [
            'seo'        => $seo,
            'menuRootId' => $menuRootId,
            'slideShow'  => isset($slideShow) ? $slideShow : [],
            'quickNav'   => $quickNav,
            'solutions'  => $solutions,
            'products'   => $products,
            'services'   => $services,
            'support'    => $support,
            'aboutUs'    => $aboutUs,
        ]);
    }
}
