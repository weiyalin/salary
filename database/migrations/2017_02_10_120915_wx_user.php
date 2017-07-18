<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WxUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wx_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid');
            $table->string('nickname');
            $table->string('avatar')->comment('微信头像');
            $table->tinyInteger('sex')->default(0)->comment('0：未知，1：男，2：女');
            $table->string('province');
            $table->string('city');
            $table->string('country');
            $table->tinyInteger('is_subscribe')->default(0)->comment('0:未关注 1：已关注');
            $table->integer('subscribe_time');
            $table->integer('create_time');
            $table->string('unionid');
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
        Schema::dropIfExists('wx_user');
    }
}
