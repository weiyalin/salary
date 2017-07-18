<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WxKeyword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wx_keyword', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->comment('关键词');
            $table->integer('reply_id')->comment('规则id');
            $table->integer('create_time');
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
        Schema::dropIfExists('wx_keyword');
    }
}
