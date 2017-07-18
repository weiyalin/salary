<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WxTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wx_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('自定义名称');
            $table->string('wx_name')->comment('微信名称');
            $table->string('template_key')->comment('自定义key');
            $table->string('template_id')->comment('微信模板id');
            $table->text('template_rule')->comment('模板配置规则（json串）[{key:显示名称,wx_key:微信模板提供,value:内容,type:0,color:"#000000",show:false,from:"占位符"}]');
            $table->text('template_demo')->comment('模板展示内容、格式，具体的值需要用template_rule中的value替换');
            $table->tinyInteger('is_system')->default(0)->comment('是否为系统通知（0：否，1：是），只有非系统通知，用户才可使用');
            $table->text('receipt_rule')->comment('回执规则[{id:0,name:""}]');
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
        Schema::dropIfExists('wx_template');
    }
}
