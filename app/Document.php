<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $keyType = 'char';    // id类型是字符
    public $incrementing = false;   // id非自增
    protected $dateFormat = 'U';    // 时间类型为时间戳

    protected $guarded = ['created_at', 'updated_at'];

    /**
     * 获取文档所属栏目
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * 获取文档附件（多对多）
     */
    public function attachment()
    {
        return $this->belongsToMany('App\Attachment', 'document_attachment');
    }

    /**
     * 获取封面图片
     */
    public function coverImage()
    {
        return $this->belongsTo('App\Attachment', 'image');
    }
}
