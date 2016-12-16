<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $model;
    protected $error;

    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * 创建模型
     */
    public function makeModel(){
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * 获取模型
     * @return 模型
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * 新增数据
     * @param array $data
     * @return model $result
     */
    public function store($data)
    {
        $result = $this->model->create($data);

        return $result;
    }

    /**
     * 获取一行数据
     * @param $id
     * @return model $result 获取的模型
     */
    public function detail($id)
    {
        $result = $this->model->findOrFail($id);

        return $result;
    }

    /**
     * 更新一行数据
     * @param array $data 更新的数据
     * @param $id 数据id
     * @return
     */
    public function update($data, $id)
    {
        $result = $this->model->where('id', $id)->update($data);

        return $result;
    }

    /**
     * 删除一行数据
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $result = $this->model->destroy($id);

        return $result;
    }

    /**
     * 获取错误信息
     */
    public function getError()
    {
        return $this->error;
    }
}
