<?php
namespace App\Repositories;

use App\Category;
use Cache;
use DB;

class CategoryRepository extends BaseRepository
{
    public function model()
    {
        return Category::class;
    }

    /**
     * 获取栏目列表
     * @return array
     */
    public function getList()
    {
        $cacheKey = config('cache.cache_keys.category_list');

        $list = Cache::rememberForever($cacheKey, function () {
            $result = $this->model->
                orderBy('level')->
                orderBy('sortord',  'desc')->
                select('id', 'parent_id', 'level', 'name', 'link', 'status', 'type')->
                get();

            return $result;
        });

        return $list;
    }

    /**
     * 获取栏目树
     * @return mixed 整个栏目树
     */
    public function tree()
    {
        $cacheKey = config('cache.cache_keys.category_tree');

        $tree = Cache::rememberForever($cacheKey, function () {
            $list = $this->getList()->toArray();

            $levelCate = array();
            foreach($list as $cate) {
                $levelCate[$cate['level']][$cate['id']] = $cate;
            }

            $root = array();

            for($level = count($levelCate); $level > 0; $level--) { // 从最底层开始构建树
                if($level > 1) { // 非顶层树枝往高一层树枝上挂
                    foreach($levelCate[$level] as $cateLow) { // 循环当前层, 取每个元素
                        foreach($levelCate[$level - 1] as &$cateHigh) { // 循环上层元素, 寻找当前元素的父
                            if($cateLow['parent_id'] == $cateHigh['id']) { // 如果发现了父
                                $cateHigh['branch'][] = $cateLow; // 将当前元素挂在父元素的 branch 字段下
                                break; // 跳出循环, 对下一个低层元素操作
                            }
                        }
                    }
                } else { // 顶层树枝往根上挂
                    foreach($levelCate[$level] as $cate) {
                        $root[] = $cate;
                    }
                }
            }

            return $root;
        });

        return $tree;
    }

    /**
     * 清理缓存
     */
    public function clearCache () {
        Cache::forget(config('cache.cache_keys.category_list'));
        Cache::forget(config('cache.cache_keys.category_tree'));
        Cache::forget(config('cache.cache_keys.category_tree_status_1'));
    }

    /**
     * 设置栏目状态
     *     设置删除状态, 检查该栏目下是否还有子栏目或者文章
     *     设置正常状态, 判断该栏目所有父栏目必须都是正常状态
     *     设置停用状态, 设置当前栏目下所有子栏目均为停用
     * @param int $cid 栏目 id
     * @param int $status 栏目状态: 0.删除 1.正常 2.停用
     * @param int $uid 操作者 uid
     * @return boolean 成功设置与否
     * @todo 删除栏目后, 栏目关联的附件需要做处理
     */
    public function setStatus($cid, $status, $uid)
    {
        switch($status) {
            default:
                $this->error = '状态错误';
                return false;
                break;
            case 0: // 删除
                // 检查子栏目
                $children = $this->children($cid);
                if(!empty($children)) {
                    $this->error = '该栏目下还有子栏目, 不能删除';
                    return false;
                }

                // 检查文档
                $docs = \App\Document::where('category_id', $cid)->where('status', '<>', 0)->count();
                if(!empty($docs)) {
                    $this->error = '该栏目下还有文章, 不能删除';
                    return false;
                }

                // 更新数据
                $result = $this->model->
                    where('id', $cid)->
                    where('status', '<>', 0)->
                    delete();
                if(false === $result) {
                    $this->error = '数据库写入错误';
                    return false;
                }

                // 删除栏目处理相应的附件
                /**
                 * @todo 处理栏目相应的附件
                 */

                break;
            case 1: // 设置正常
                // 判断有无状态不为正常的父栏目
                $parents = $this->parents($cid);
                if(is_array($parents)) foreach($parents as $cate) {
                    if($cate['status'] != 1) {
                        $this->error = '该栏目有“状态不为正常的”父栏目';
                        return false;
                    }
                }

                // 更新数据
                $result = $this->model->
                    where('id', $cid)->
                    where('status', '<>', 0)->
                    update([
                        'status'           => 1,
                        'user_id_modify'  => $uid,
                    ]);
                if(false === $result) {
                    $this->error = '数据库写入错误';
                    return false;
                }
                break;
            case 2: // 设置停用
                $cids = array($cid);
                // 取所有子栏目
                $children = $this->children($cid);
                if(is_array($children)) foreach($children as $cate) {
                    $cids[] = $cate['id'];
                }
                $result = $this->model->
                    whereIn('id', $cids)->
                    where('status', '<>', 0)->
                    update([
                        'status'           => 2,
                        'user_id_modify'  => $uid,
                    ]);
                if(false === $result) {
                    $this->error = '数据库写入错误';
                    return false;
                }
                break;
        }

        // 清理缓存
        $this->clearCache();

        return true;
    }

    /**
     * 根据 cid 找到某个树枝
     * @param $cid 要找的栏目 id
     * @param $root 要寻找的树, 为空时寻找整棵树
     * @return mixed
     */
    public function branch($cid, $root = null)
    {
        // $cid 为零时返回整个树
        if($cid == 0) {
            return $this->tree();
        }

        if(null === $root) {
            $root = $this->tree();
        }

        foreach($root as $branch) {
            if($branch['id'] == $cid) {
                return $branch;
            }
            if(isset($branch['branch'])) {
                $result = $this->branch($cid, $branch['branch']);
                if($result) {
                    return $result;
                }
            }
        }

        return false;
    }

    // 递归子元素
    private function myChildren($root, &$children)
    {
        if(isset($root['branch'])) {
            foreach($root['branch'] as $branch) {
                $this->myChildren($branch, $children);
            }
            unset($root['branch']);
        }
        $children[] = $root;
        return $children;
    }

    /**
     * 获取某个元素的所有子栏目列表
     * @param int $cid 元素 id
     * @return array
     */
    public function children($cid)
    {
        $root = $this->branch($cid);

        if($cid == 0) {
            $root['branch'] = $root;
        }

        if(isset($root['branch'])) {
            $children = array();
            foreach($root['branch'] as $branch) {
               $this->myChildren($branch, $children);
            }
        } else {
            $children = false;
        }

        return $children;
    }

    /**
     * 获取某个元素的所有父栏目列表
     * @param int $cid 元素 id
     * @return array
     */
    public function parents($cid)
    {
        $listArr = $this->getList()->toArray();

        foreach ($listArr as $value) {
            $list[$value['id']] = $value;
        }

        $parents = array();
        $currentCid = $cid;

        while(1) {
            if(!isset($list[$currentCid]) || !isset($list[$list[$currentCid]['parent_id']])) {
                break;
            }
            $parents[] = $list[$list[$currentCid]['parent_id']];
            $currentCid = $list[$currentCid]['parent_id'];
        }

        return $parents ?: false;
    }

    /**
     * 编辑栏目
     * @param mixed $data 栏目信息
     * @param int $id 栏目id
     * @param boolean 是否编辑成功
     */
    public function modify($data, $id)
    {
        $data['parent_id'] = isset($data['parent_id']) ? $data['parent_id'] : 0;

        // 父栏目不能为自己
        if($data['parent_id'] == $id) {
            $this->error = '栏目层级关系错误';
            return false;
        }

        // 判断原父栏目状态
        if($data['parent_id'] === 0) {
            $parent = null;
        } else {
            $parent = $this->model->find($data['parent_id']);
            if(!$parent) {
                $this->error = '父栏目不存在';
                return false;
            }
        }

        // 取旧数据, 如果父栏目发生变化, 验证合理性, 并处理子栏目的栏目级别
        $oldInfo = $this->model->where('id', $id)->first();
        if($oldInfo->parent_id != $data['parent_id']) {
            // 取当前栏目的所有子栏目
            $children = $this->children($id);
            $toUpdateCids = array(); // 需要更新的栏目 id
            if(!empty($children)) {
                foreach($children as $cate) {
                    // 如果新的父栏目是子栏目中的一个, 退出
                    if($cate['id'] == $data['parent_id']) {
                        $this->error = '栏目层级关系错误';
                        return false;
                    }
                    $toUpdateCids[] = $cate['id'];
                }
            }

            // 新的栏目级别
            if($parent) {
                $newLevel = $parent->level + 1;
            } else {
                $newLevel = 1;
            }

            // 需要更新的级别
            $updateLevel = $newLevel - $oldInfo->level;
            if($updateLevel && !empty($toUpdateCids)) {
                $update['level'] = DB::raw("`level` + ({$updateLevel})");

                // 如果父栏目为禁用，则所有子栏目都要禁用
                if($parent && $parent->status == 2) {
                    $update['status'] = 2;
                }
                $this->model->whereIn('id', $toUpdateCids)->update($update);
            }

            // 处理当前栏目级别
            $data['level'] = $newLevel;
            if($parent && $parent->status == 2) {
                $data['status'] = 2;
            }
        }

        $result = $this->model->where('id', $id)->update($data);

        if($result === false) {
            $this->error = '数据库写入错误';
            return false;
        }

        // 清理缓存
        $this->clearCache();

        return true;
    }

    /**
     * 新增栏目
     * @param array $data 栏目信息
     * @return int 新增栏目 id
     */
    public function store($data) {
        // 父栏目情况
        if(isset($data['parent_id']) && $data['parent_id'] > 0) {
            // 判断父栏目
            $parent = $this->model->find($data['parent_id']);
            if($parent->status != 1) {
                $this->error = '该栏目有“状态不为正常的”父栏目';
                return false;
            }

            // 判断祖先栏目
            $parents = $this->parents($data['parent_id']);
            if(is_array($parents)) foreach($parents as $cate) {
                if($cate['status'] != 1) {
                    $this->error = '该栏目有“状态不为正常的”祖先栏目';
                    return false;
                }
            }

            // 处理栏目级别
            $data['level'] = ++$parent->level;
        } else {
            $data['level'] = 1;
        }


        $result = $this->model->create($data);

        // 清理缓存
        $this->clearCache();

        return $result->id ?: false;
    }

    /**
     * 获取某树的两层（状态为正常）
     * @param int $parent_id 父id
     * @return array 栏木树
     */
    public function treeLevel2($parent_id)
    {
        $cacheKey = config('cache.cache_keys.category_tree_status_1');

        $result = Cache::rememberForever($cacheKey, function () use ($parent_id) {
            $parent = $this->model->find($parent_id);
            $level = [$parent->level + 1, $parent->level + 2];

            $list = $this->children($parent_id);

            $tree = [];

            if($list) {
                foreach ($list as $value) {
                    if($value['status'] == 1 && $value['level'] == $level[0]) {
                        $tree[$value['id']] = $value;
                    }
                }

                foreach ($list as $value) {
                    if($value['status'] == 1 && $value['level'] == $level[1])
                    $tree[$value['parent_id']]['sub'][] = $value;
                }
            }

            return $tree;
        });

        return $result;
    }

    /**
     * 主菜单根
     * @param int $category_id
     * @return 根栏目模型
     */
    public function menuRoot($category_id)
    {
        $category = $this->model->find($category_id);

        if($category->level > 2) {
            $category = $this->menuRoot($category->parent_id);
        }

        return $category;
    }

    /**
     * 设置文档为栏目描述
     * @param int $category_id 栏目id
     * @param int $document_id 文档id
     */
    public function setDocument($category_id, $document_id)
    {
        $category = $this->model->find($category_id);

        if(!$category) {
            $this->error = '栏目不存在';
        }

        $category->document_id = $document_id;
        $result = $category->save();

        return $result;
    }
}
