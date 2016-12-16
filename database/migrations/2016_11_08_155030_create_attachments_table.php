<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyinteger('type')->comment('类型: 1.图片 2.文件 3.视频');
            $table->integer('refer')->default(0)->comment('引用次数');
            $table->string('description', 500)->default('')->comment('附件描述');
            $table->char('md5', 32)->comment('md5哈希');
            $table->string('ext', 10)->comment('扩展名');
            $table->unsignedInteger('size')->default(0)->comment('附件大小');
            $table->bigInteger('created_at')->comment('创建时间');
            $table->bigInteger('updated_at')->comment('更新时间');
            $table->integer('user_id_create')->unsigned()->comment('创建者 id');
            $table->integer('user_id_modify')->unsigned()->comment('最后编辑者 id');
            $table->string('uri', 200)->comment('存放路径');
            $table->unsignedInteger('width')->default(0)->comment('图片宽');
            $table->unsignedInteger('height')->default(0)->comment('图片高');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
