<?php
// 显示分类选择器
function categorySelector($selectId, $selectName, $selectClass, $value, $showRoot = false)
{
    $category = app(App\Repositories\CategoryRepository::class);

    $tree = $category->tree();
    $select = array(
        'id'    => $selectId,
        'name'  => $selectName,
        'class' => $selectClass,
        'value' => $value,
    );

    echo view('admin.category.widgetSelector', compact('tree', 'select', 'showRoot'))->render();
}

/**
 * 获取文件路径
 * @param string $md5 附件MD5
 * @param string $ext 附件后缀名
 * @param string $action 动作：set：创建路径, 否则获取路径
 */
function attachmentUri($md5, $ext, $action = 'get') {
    $rootPath = config('upload.attachment.path');   // 附件根路径
    $relativePath = '.' . $rootPath;
    $dir = substr($md5, 0, 2);

    // 将新文件以md5去掉前两位字符命名
    $newName = substr($md5, 2) . '.' . $ext;

    // 文件路径
    $path = $rootPath . '/' . $dir . '/' . $newName;

    return [
        'dir'      => $rootPath . '/' . $dir,
        'filename' => $newName,
        'path'     => $path,
    ];
}

/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path)) return null;
    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $path2 = $path . $file;
            if (is_dir($path2)) {
                getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                    $files[] = array(
                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime'=> filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}

// 输出栏目树
function outputTree ($tree) {
    foreach($tree as $leaf) {
        echo '<ul class="list-unstyled">';
        echo '<li>';
        echo     '<div class="info clearfix">';
        echo         '<span class="glyphicon glyphicon-chevron-right menu-icon"></span>';

        if($leaf['status'] == 1) {
            echo         '<span class="name">';
        } else {
            echo         '<span class="name status-disabled">';
        }

        echo                 '[' . config('system.category_type')[$leaf['type']] . '-' . $leaf['id'] . '] ' . $leaf['name'];
        echo             '</span>';

        echo         '<div class="button-box pull-right" style="display:none">';
        echo             '<button type="button" class="btn btn-primary btn-xs add" data-id="' . $leaf['id'] . '" data-placement="top" title="添加子栏目">';
        echo                 '<span class="glyphicon glyphicon-plus"></span>';
        echo             '</button>';
        echo             '<button type="button" class="btn btn-info btn-xs edit" data-id="' . $leaf['id'] . '" data-placement="top" title="编辑本栏目">';
        echo                 '<span class="glyphicon glyphicon-edit"></span>';
        echo             '</button>';

        if($leaf['status'] == 1) {
            echo         '<button type="button" class="btn btn-warning btn-xs set-status" data-status="2" data-id="' . $leaf['id'] . '" data-placement="top" title="禁用本栏目">';
            echo             '<span class="glyphicon glyphicon-ban-circle"></span>';
            echo         '</button>';
        } else {
            echo         '<button type="button" class="btn btn-success btn-xs set-status" data-status="1" data-id="' . $leaf['id'] . '" data-placement="top" title="启用本栏目">';
            echo             '<span class="glyphicon glyphicon-ok-circle"></span>';
            echo         '</button>';
        }

        echo             '<button type="button" class="btn btn-danger btn-xs set-status"  data-status="0" data-id="' . $leaf['id'] . '" data-placement="top" title="删除本栏目及子栏目">';
        echo                 '<span class="glyphicon glyphicon-remove-circle"></span>';
        echo             '</button>';
        echo         '</div>';
        echo     '</div>';
        if(isset($leaf['branch'])) {
            outputTree($leaf['branch']);
        }
        echo "</li>";
        echo '</ul>';
    }
}

// 输出栏目选择控件
function selectTree ($tree, $value) {
    foreach($tree as $leaf) {
        $tab = '';
        $tab = str_repeat('&nbsp;', ($leaf['level']) * 4);
        if($value == $leaf['id']) {
            echo '<option value="' . $leaf['id'] . '" selected>' . $tab . $leaf['name'] . '</option>';
        } else {
            echo '<option value="' . $leaf['id'] . '">' . $tab . $leaf['name'] . '</option>';
        }

        if(isset($leaf['branch'])) {
            selectTree($leaf['branch'], $value);
        }
    }
}
