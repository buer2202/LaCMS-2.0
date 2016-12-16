<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\DocumentRepository;
use App\Repositories\CategoryRepository;

class DocumentController extends Controller
{
    public function index(DocumentRepository $document, CategoryRepository $category, $id)
    {
        // 不能是栏目介绍
        $cate = $category->getModel()->where('document_id', $id)->first();
        if($cate) {
            abort(404);
        }

        // 文档信息
        $doc = $document->getModel()->where('status', 1)->findOrFail($id);
        $seo = [
            'title'       => $doc->seo_title ?: $doc->title,
            'keywords'    => $doc->seo_keywords,
            'description' => $doc->seo_description,
        ];

        // 栏目信息
        $category_id = $doc->category_id;
        $cate = $category->detail($category_id);
        $parents = $category->parents($category_id);

        // 栏目根
        $menuRootId = $category->menuRoot($cate->id)->id;

        // 边栏菜单
        if($cate->level == 2) {
            // 如果是顶级分类，菜单为子分类
            $menu = $category->getModel()->
                where('level', $cate->level + 1)->
                where('parent_id', $cate->id)->
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
        krsort($crumbs);
        array_shift($crumbs);

        // 默认视图
        $tpl = 'home.document.' . $doc->template;

        // 如果不存在，默认使用index模板
        if(!view()->exists($tpl)) {
            $tpl = 'home.document.index';
        }

        return view($tpl, [
            'cate'       => $cate,
            'seo'        => $seo,
            'menuTitle'  => $menuTitle,
            'menu'       => $menu,
            'menuRootId' => $menuRootId,
            'crumbs'     => $crumbs,
            'data'       => $doc,
        ]);
    }
}
