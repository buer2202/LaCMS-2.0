<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $dateFormat = 'U';
    protected $guarded = ['created_at', 'updated_at'];
}
