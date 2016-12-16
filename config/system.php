<?php
// 内容管理系统配置
return [
    // 栏目类型
    'category_type' => [
        1 => '群组',
        2 => '列表',
        3 => '文章',
        4 => '其他',
    ],

    // 栏目类型对应的模板路径
    'category_type_tpl' => [
        1 => 'group',
        2 => 'list',
        3 => 'detail',
        4 => 'other',
    ],

    // 栏目状态
    'category_status' => [
        0 => '删除',
        1 => '正常',
        2 => '禁用',
    ],

    // 文档状态
    'document_status' => [
        1 => '正常',
        2 => '禁用',
    ],

    // 各模块栏目id
    'category_id' => [
        'main_menu'   => 1, // 主菜单
        'recycle_bin' => 2, // 回收站

        // 首页群组
        'index' => [
            'id' => 3,
            'group' => [
                'slide_show' => 4, // 图片轮播
                'quick_nav'  => 5, // 快捷导航
                'solutions'  => 6, // 解决方案
                'products'   => 7, // 产品
                'services'   => 8, // 服务
                'support'    => 9, // 技术支持
                'about_us'   => 10, // 关于我们
            ],
        ],
    ],
];
