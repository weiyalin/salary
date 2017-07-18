<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50)->comment('用户名');
            $table->string('phone',20);
            $table->char('password',32)->comment('密码');
            $table->char('salt',4)->comment('盐值，长度4位');
            $table->tinyInteger('status')->default(0)->comment('状态（0：正常，1：删除）');
            $table->integer('create_time');
            $table->integer('update_time')->nullable();
            $table->integer('role_id')->default(0)->comment('角色id');
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
        Schema::dropIfExists('user');
    }
}
