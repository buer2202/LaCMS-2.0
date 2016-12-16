<?php
namespace App\Repositories;

use App\DocumentAttachment;

class DocumentAttachmentRepository extends BaseRepository
{
    public function model()
    {
        return DocumentAttachment::class;
    }

    /**
     * 新增附件
     * @param array $data 附件信息
     * @return model 新增附件模型
     */
    public function store($data)
    {
        // 不存在就插入
        $result = $this->model->firstOrCreate($data);

        return $result;
    }

    /**
     * 附件关联
     * @param string $document_id 文档id
     */
    public function relation($document_id) {
        $attachment = new AttachmentRepository;

        // 更新附件信息
        $this->model->where('document_id', $document_id)->update(['effective' => 1]);

        // 获取当前附件列表
        $currentAttachmentIds = $this->model->where('document_id', $document_id)->where('effective', 1)->pluck('attachment_id')->toArray();

        // 增加引用次数
        $attachment->refer($currentAttachmentIds, 'inc');

        return true;
    }

    /**
     * 删除关联
     * @param int $document_id 文档id
     * @param int $attachment_id 附件id
     */
    public function deleteRelation($document_id, $attachment_id) {
        $model = $this->model->where('document_id', $document_id)->where('attachment_id', $attachment_id)->first();

        if(!$model) {
            $this->error = '没有该关联信息';
            return false;
        }

        // 删除记录
        $model->delete();

        // 减少引用次数
        $attachment = new AttachmentRepository;
        $attachment->refer($attachment_id, 'dec');

        return true;
    }

    /**
     * 清楚无效关联
     */
    public function clearInvalidRelation() {
        $result = $this->model->where('effective', 0)->delete();

        return $result;
    }
}
