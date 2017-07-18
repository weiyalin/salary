<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Node extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('node', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50)->comment('权限节点名称');
            $table->string('code',50)->comment('节点编号，如001,002001');
            $table->integer('pid')->default(0)->comment('父级节点id');
            $table->tinyInteger('depth')->default(1)->comment('菜单层级；1：一级菜单 2：二级 3：页面节点');
            $table->string('path')->default('#')->comment('节点路由');
            $table->tinyInteger('type')->comment('节点类型（0：menu 1:button 2:api）');
            $table->tinyInteger('sort_factor')->comment('排序因子，越小越靠前');
            $table->string('icon',50)->nullable();
            $table->integer('create_time')->default(0);
            $table->integer('update_time')->nullable();
            $table->tinyInteger('status')->default(0)->comment('状态（0：正常 1：删除）');
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
        Schema::dropIfExists('node');
    }
}
