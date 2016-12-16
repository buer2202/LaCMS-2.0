<?php

use Illuminate\Database\Seeder;

use App\Category;
use App\Document;
use App\DocumentAttachment;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 主菜单群组
        factory(Category::class)->create([
            'id'      => config('system.category_id.main_menu'),
            'name'    => '主菜单',
            'sortord' => 9999,
        ]);

        // 回收站
        factory(Category::class)->create([
            'id'        => config('system.category_id.recycle_bin'),
            'name'      => '回收站',
            'sortord'   => -9999,
        ]);

        // 首页栏目群组
        factory(Category::class)->create([
            'id'      => config('system.category_id.index.id'),
            'name'    => '首页栏目',
            'sortord' => 80,
        ]);

        /************************************************************************
         首页栏目内容-开始
        ************************************************************************/
        // 轮播图片
        $slide_show = factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.slide_show'),
            'parent_id' => config('system.category_id.index.id'),
            'level'     => 2,
            'name'      => '图片轮播',
            'sortord'   => 100,
        ]);

        $doc = factory(Document::class)->create(['category_id' => $slide_show->id]);

        $slide_show->document_id = $doc->id;
        $slide_show->save();

        factory(DocumentAttachment::class)->create(['document_id' => $doc->id]);
        factory(DocumentAttachment::class)->create(['document_id' => $doc->id, 'attachment_id' => 2]);

        // 快捷导航
        $quick_nav = factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.quick_nav'),
            'parent_id' => config('system.category_id.index.id'),
            'level'     => 2,
            'name'      => '快捷导航',
            'sortord'   => 99,
        ]);

        /************************************************************************
         首页栏目内容-结束
        ************************************************************************/

        /************************************************************************
         主菜单内容-开始
        ************************************************************************/
        factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.solutions'),
            'parent_id' => config('system.category_id.main_menu'),
            'name'      => '解决方案',
            'level'     => 2,
            'type'      => 2,
            'sortord'   => 100,
        ]);

        factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.products'),
            'parent_id' => config('system.category_id.main_menu'),
            'name'      => '产品',
            'level'     => 2,
            'type'      => 2,
            'sortord'   => 90,
        ]);

        factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.services'),
            'parent_id' => config('system.category_id.main_menu'),
            'name'      => '服务',
            'level'     => 2,
            'type'      => 2,
            'sortord'   => 80,
        ]);

        factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.support'),
            'parent_id' => config('system.category_id.main_menu'),
            'name'      => '技术支持',
            'level'     => 2,
            'type'      => 2,
            'sortord'   => 70,
        ]);

        factory(Category::class)->create([
            'id'        => config('system.category_id.index.group.about_us'),
            'parent_id' => config('system.category_id.main_menu'),
            'name'      => '关于我们',
            'level'     => 2,
            'type'      => 3,
            'sortord'   => 60,
        ]);

        // 给每个栏目配置 描述文档 子栏目
        foreach (Category::where('level', 2)->get() as $cate) {
            // 添加文档
            if($cate->type === 2) {
                factory(Document::class, 30)->create(['category_id' => $cate->id]);
            } elseif($cate->type === 3) {
                factory(Document::class)->create(['category_id' => $cate->id]);
            } else {
                continue;
            }

            // 设置描述文档
            $cate->document_id = Document::where('category_id', $cate->id)->first()->id;
            $cate->save();

            // 添加关联附件
            $documents = Document::where('category_id', $cate->id)->get();
            foreach ($documents as $value) {
                factory(DocumentAttachment::class)->create(['document_id' => $value->id]);
                factory(DocumentAttachment::class)->create(['document_id' => $value->id, 'attachment_id' => 2]);
            }

            // 添加子栏目
            factory(Category::class, 2)->create([
                'parent_id' => $cate->id,
                'level'     => 3,
                'type'      => 2,
            ]);

            factory(Category::class, 2)->create([
                'parent_id' => $cate->id,
                'level'     => 3,
                'type'      => 3,
            ]);
        }

        // 给每个子栏目配置 描述文档
        foreach (Category::where('level', 3)->get() as $cate) {
            // 添加文档
            if($cate->type === 2) {
                factory(Document::class, 30)->create(['category_id' => $cate->id]);
            } elseif($cate->type === 3) {
                factory(Document::class)->create(['category_id' => $cate->id]);
            } else {
                continue;
            }

            // 设置描述文档
            $cate->document_id = Document::where('category_id', $cate->id)->first()->id;
            $cate->save();
        }
        /************************************************************************
         主菜单内容-结束
        ************************************************************************/

        // 快捷导航内容
        factory(Category::class, 6)->create([
            'parent_id' => $quick_nav->id,
            'level'     => 3,
            'link'      => 'http://www.baidu.com/',
        ]);
    }
}
