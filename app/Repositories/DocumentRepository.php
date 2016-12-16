<?php
namespace App\Repositories;

use App\Document;

class DocumentRepository extends BaseRepository
{
    public function model()
    {
        return Document::class;
    }

    /**
     * 获取文档列表
     */
    public function getList($category_id, $title)
    {
        $dataList = $this->model
            ->join('categories as c', 'documents.category_id', '=', 'c.id')
            ->select('documents.*', 'c.name as category_name')
            ->when($category_id, function ($query) use ($category_id) {
                return $query->where('documents.category_id', $category_id);
            })
            ->when($title, function ($query) use ($title) {
                return $query->where('documents.title', 'like', "%{$title}%");
            })
            ->orderBy('id', 'desc')
            ->paginate();

        return $dataList;
    }

    /**
     * 获取文档附件
     * @param char 文档id
     * @param int 附件类型
     * @return Collection 结果集合
     */
    public function getAttachment($id, $type)
    {
        $data = $this->model->find($id);

        if($data) {
            $data = $data->attachment()->where('type', $type)->get();
        }

        return $data;
    }

    /**
     * 获取ueditor的附件格式
     * @param char 文档id
     * @param int 附件类型
     * @return array 结果数组
     */
    public function getAttachmentForUeditor($id, $type)
    {
        $result = $this->getAttachment($id, $type);

        $data = array();
        if($result) {
            foreach ($result as $value) {
                $data[] = [
                    'url'   => $value->uri,
                    'mtime' => $value->updated_at->timestamp,
                ];
            }
        }

        return $data;
    }
}
