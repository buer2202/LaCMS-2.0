<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentAttachment extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'document_attachment';
    public $timestamps = false;
    protected $guarded = [];    // 设置黑名单为空
}
