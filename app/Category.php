<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $dateFormat = 'U';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    /**
     * 获取栏目的文档
     */
    public function documents()
    {
         return $this->hasMany('App\Document');
    }

    /**
     * 获取栏目的主文档
     */
    public function document()
    {
         return $this->belongsTo('App\Document');
    }
}
