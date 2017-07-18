<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Feedback extends Migration
{
    /**
     * 创建反馈表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message');
            $table->string('path');
            $table->string('useragent');
            $table->ipAddress('ip');
            $table->timestamps();
        });
    }

    /**
     * 删除反馈表
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
