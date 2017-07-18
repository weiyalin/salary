<?php

use Illuminate\Database\Seeder;

class NodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::insert("
            INSERT INTO `node` (name,code,pid,depth,path,type,sort_factor,icon,create_time)
            VALUES
            ('微信模块', '001', '0', '1', '#', '0', '0', 'el-icon-message', '0'),
            ('微信基础配置', '001001', '1', '2', '/wx/config', '0', '0', '', '0'),
            ('模板消息配置', '001002', '1', '2', '/wx/template', '0', '0', '', '0'),
            ('微信菜单配置', '001003', '1', '2', '/wx/menu', '0', '0', '', '0'),
            ('自动回复配置', '001004', '1', '2', '/wx/reply', '0', '0', '', '0'),
            ('用户模块', '002', '0', '1', '#', '0', '0', 'el-icon-menu', '0'),
            ('用户管理', '002001', '6', '2', '/user/users', '0', '0', '', '0'),
            ('角色管理', '002002', '6', '2', '/role/list', '0', '0', '', '0'),
            ('反馈模块', '003', '0', '1', '#', '0', '0', 'el-icon-document', '0'),
            ('用户反馈', '003001', '9', '2', '/feedback/list', '0', '0', '', '0')
        ");
    }
}
