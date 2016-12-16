<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\CategoryRepository;

class CategoryController extends Controller
{
    public function index(CategoryRepository $category, $id)
    {
        // 树根是【主菜单】下的栏目
        $parents = $category->parents($id);
        if($parents && end($parents)['id'] == config('system.category_id.main_menu')) {
            krsort($parents);
        } else {
            abort(404);
        }

        // 栏目信息
        $cate = $category->getModel()->where('id', $id)->where('status', 1)->first();
        if(!$cate) {
            abort(404);
        }

        // 栏目根
        $menuRootId = $category->menuRoot($id)->id;

        $seo = [
            'title'       => $cate->seo_title ?: $cate->name,
            'keywords'    => $cate->seo_keywords,
            'description' => $cate->seo_description,
        ];

        // 边栏菜单
        if($cate->level == 2) {
            // 如果是顶级分类，菜单为子分类
            $menu = $category->getModel()->
                where('level', $cate->level + 1)->
                where('parent_id', $id)->
                where('status', 1)->
                get();

            $menuTitle = $cate->name;
        } else {
            // 如果是子分类，菜单是同级分类
            $menu = $category->getModel()->
                where('level', $cate->level)->
                where('parent_id', $cate->parent_id)->
                where('status', 1)->
                get();

            foreach ($parents as $value) {
                if($cate->level - $value['level'] == 1) {
                    $menuTitle = $value['name'];
                    break;
                }
            }
        }

        // 面包屑
        $crumbs = $parents;
        array_shift($crumbs);

        // 视图
        $typeName = config('system.category_type_tpl')[$cate->type];
        $tpl = "home.category.{$typeName}.{$cate['template']}";

        // 内容
        switch ($typeName) {
            case 'list':
                $data = $cate->documents()->where('status', 1)->where('id', '<>', $cate->document_id)->orderBy('sortord', 'desc')->orderBy('id', 'desc')->paginate(20);
                break;
            case 'detail':
                $data = $cate->document;
                break;
            default:
                $data['list'] = $cate->documents()->where('status', 1)->where('id', '<>', $cate->document_id)->orderBy('sortord', 'desc')->orderBy('id', 'desc')->paginate(20);
                $data['detail'] = $cate->document;
                break;
        }

        // 如果不存在，404
        if(!view()->exists($tpl)) {
            abort(404);
        }

        return view($tpl, compact('cate', 'seo', 'menuTitle', 'menu', 'menuRootId', 'crumbs', 'data'));
    }
}
