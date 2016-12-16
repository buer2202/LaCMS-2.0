<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->default(0)->comment('父栏目 id, 默认 0, 为根栏目');
            $table->tinyInteger('level')->unsigned()->default(1)->comment('栏目层级');
            $table->string('name', 100)->comment('栏目名称');
            $table->tinyinteger('type')->default(1)->comment('类型：1.群组；2.列表；3.专题；4.混合');
            $table->char('document_id', 13)->default('')->comment('栏目介绍的文档id');
            $table->tinyInteger('status')->default(1)->comment('状态: 1.正常 2.禁用');
            $table->integer('sortord')->default(0)->comment('排序');
            $table->string('link', 500)->default('')->comment('栏目链接');
            $table->string('template', 50)->default('index')->comment('模板');
            $table->string('seo_title', 100)->default('')->comment('seo 标题');
            $table->string('seo_keywords', 100)->default('')->comment('seo 关键字');
            $table->string('seo_description', 500)->default('')->comment('seo 描述');
            $table->string('info_1', 500)->default('')->comment('备选信息1');
            $table->string('info_2', 500)->default('')->comment('备选信息2');
            $table->string('info_3', 500)->default('')->comment('备选信息3');
            $table->string('info_4', 500)->default('')->comment('备选信息4');
            $table->string('info_5', 500)->default('')->comment('备选信息5');
            $table->string('info_6', 500)->default('')->comment('备选信息6');
            $table->integer('user_id_create')->unsigned()->comment('创建者 id');
            $table->integer('user_id_modify')->unsigned()->comment('最后编辑者 id');
            $table->bigInteger('created_at')->comment('创建时间');
            $table->bigInteger('updated_at')->comment('更新时间');
            $table->bigInteger('deleted_at')->nullable()->comment('删除时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
