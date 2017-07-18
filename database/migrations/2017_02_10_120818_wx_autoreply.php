<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WxAutoreply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wx_autoreply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',100)->comment('规则名称');
            $table->tinyInteger('category')->default(0)->comment('类型，0：被添加自动回复(关注时回复)，1：消息自动回复（关键字匹配不到回复），2：关键词自动回复');
            $table->tinyInteger('type')->default(0)->comment('类型：1.文本,2.事件');
            $table->string('event')->nullable()->comment('事件');
            $table->text('content')->comment('回复内容');
            $table->tinyInteger('status')->default(0)->comment('状态（0：正常，1：已删除）');
            $table->integer('create_time')->nullable();
            $table->integer('update_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('wx_autoreply');
    }
}
