<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_attachment', function (Blueprint $table) {
            $table->increments('id');
            $table->char('document_id', 13);
            $table->unsignedInteger('attachment_id');
            $table->unsignedTinyInteger('effective')->default(0)->comment('是否有效');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_attachment');
    }
}
