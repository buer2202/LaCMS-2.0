<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->char('id', 13)->comment('文章id，13位字符');
            $table->primary('id');
            $table->integer('category_id')->comment('所属栏目id');
            $table->string('filename', 50)->default('')->comment('文档 url 结尾文件名');
            $table->integer('sortord')->unsigned()->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态: 1.正常 2.禁用');
            $table->string('title', 200)->comment('文档标题');
            $table->string('title_sub', 200)->default('')->comment('文档子标题');
            $table->string('template', 50)->default('')->comment('页面模板');
            $table->integer('image')->unsigned()->default(0)->comment('封面图片 attachment.id');
            $table->mediumText('content')->comment('内容');
            $table->string('seo_title', 200)->default('')->comment('seo 标题');
            $table->string('seo_keywords', 200)->default('')->comment('seo 关键字');
            $table->string('seo_description', 200)->default('')->comment('seo 描述');
            $table->bigInteger('time_document')->default(0)->comment('文档时间');
            $table->integer('user_id_create')->unsigned()->comment('创建者 id');
            $table->integer('user_id_modify')->unsigned()->comment('最后编辑者 id');
            $table->string('info_1', 500)->default('')->comment('备选信息1');
            $table->string('info_2', 500)->default('')->comment('备选信息2');
            $table->string('info_3', 500)->default('')->comment('备选信息3');
            $table->string('info_4', 500)->default('')->comment('备选信息4');
            $table->string('info_5', 500)->default('')->comment('备选信息5');
            $table->string('info_6', 500)->default('')->comment('备选信息6');
            $table->bigInteger('created_at')->comment('创建时间');
            $table->bigInteger('updated_at')->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
