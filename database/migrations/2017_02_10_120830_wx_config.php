<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WxConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wx_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appid',100);
            $table->string('appsecret',200);
            $table->string('appname',50)->nullable()->comment('公众号名称');
            $table->string('subscribe_url',500)->comment('关注公众号引导页URL');
            $table->string('dev_url',500);
            $table->string('dev_token',200);
            $table->string('dev_aeskey',200);
            $table->tinyInteger('dev_encrypt_type')->default(0)->comment('0：明文，1：兼容，2：安全');
            $table->string('open_appid',100)->comment('微信开放平台appid');
            $table->string('open_appsecret',200)->comment('微信开放平台appsecret');
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
        Schema::dropIfExists('wx_config');
    }
}
