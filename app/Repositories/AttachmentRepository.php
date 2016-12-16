<?php
namespace App\Repositories;

use App\Attachment;
use App\DocumentAttachment;

class AttachmentRepository extends BaseRepository
{
    public function model()
    {
        return Attachment::class;
    }

    /**
     * 新增附件
     * @param array $document_id 文档id
     * @param array $action 编辑文档的状态：store 或 update
     * @param array $data 附件信息
     * @return model 新增附件模型
     */
    public function save($document_id, $action, $data)
    {
        $relativePath = '.' . $data['uri']; // 相对路径

        // 检索md5码
        $md5 = md5_file($relativePath);
        $attachment = $this->model->where('md5', $md5)->first();

        if($attachment) {   // 如果已存在记录
            // 如果是编辑文档的状态
            if($action == 'update') {
                // 检查此附件是否与此文档有关联
                $result = DocumentAttachment::where('document_id', $document_id)->where('attachment_id', $attachment->id)->first();

                // 如果没有关联，附件引用次数+1
                if(!$result) {
                    $this->refer($attachment->id, 'inc');
                }
            }

            // 更新一下说明
            $attachment->description = $data['description'];
            $attachment->save();

            // 删掉上传的文件
            unlink($relativePath);
        } else {    // 不存在记录
            $data['md5'] = $md5;
            if($data['type'] == 1) {
                $imageSize = getimagesize($relativePath);
                $data['width']  = $imageSize[0];
                $data['height'] = $imageSize[1];
            }

            $attachment = $this->store($data);

            if(!$attachment) {
                unlink($relativePath);
                $this->error = '数据库写入失败';
                return false;
            }

            // 如果是编辑，附件引用次数+1
            if($action == 'update') {
                $this->refer($attachment->id, 'inc');
            }
        }

        return $attachment;
    }

    /**
     * 更新引用
     * @param int/array id 附件id
     * @param string incOrDec 值为'inc'是增加，'dec'减少
     * @return
     */
    public function refer($id, $incOrDec) {
        if(is_array($id)) {
            $model = $this->model->whereIn('id', $id);
        } else {
            $model = $this->model->where('id', $id);
        }

        switch ($incOrDec) {
            case 'inc':
                $result = $model->increment('refer');
                break;
            case 'dec':
                $result = $model->decrement('refer');
                break;
            default:
                $this->error = '更新操作不能识别';
                return false;
        }

        if(false === $result) {
            $this->error = '数据库写入失败';
            return false;
        }

        return true;
    }

    /**
     * 获取引用次数为0的附件列表
     */
    public function notRefer($pageSize)
    {
        $result = $this->model->where('refer', 0)->paginate($pageSize);

        return $result;
    }

    /**
     * 批量删除
     * @param array ids 附件id
     * @return boolean 是否成功
     */
    public function batchDelete($ids)
    {
        $data = $this->model->whereIn('id', $ids)->where('refer', 0)->pluck('id');

        if(empty($data)) {
            $this->error = '不存在该附件';
            return false;
        }

        foreach ($data as $id) {
            if(!$this->destroy($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 删除附件
     * @param int id 附件id
     * @return boolean 是否成功
     */
    public function destroy($id)
    {
        $data = $this->model->where('id', $id)->first();

        if(empty($data)) {
            $this->error = '不存在该附件';
            return false;
        }

        // 删文件
        $file = '.' . $data->uri;
        if(file_exists($file)) {
            unlink($file);
        }

        // 删记录
        if(!$data->delete()) {
            $this->error = '删除失败';
            return false;
        }

        return true;
    }
}
