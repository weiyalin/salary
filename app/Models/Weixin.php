<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9 0009
 * Time: 17:06
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Log;

class Weixin
{
    private static $wx_config_id = 1;

    /**
     * 获取微信基本配置
     * @return mixed
     */
    public static function getConfig()
    {
        try
        {
            $config = DB::table("wx_config")->where(array("id" => Weixin::$wx_config_id))->first();
            return $config;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 设置微信基本配置
     * @param $config
     * @return mixed
     */
    public static function setConfig($config)
    {
        try
        {
            $rows = DB::table("wx_config")->where(array("id" => Weixin::$wx_config_id))->first();
            if ($rows == null) {
                $rows = DB::table("wx_config")->insert($config);
            } else {
                $rows = DB::table("wx_config")->where(array("id" => Weixin::$wx_config_id))->update($config);
            }
            return $rows;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }

    /**
     * 获取所有模板
     * @return mixed
     */
    public static function get_wx_template()
    {
        try
        {
            $rows = DB::table("wx_template")->get();
            return $rows;
        } catch (\Exception $e) {
            Log::info($e);
            return [];
        }
    }

    /**
     * 手动添加模板
     * @param $template
     * @return mixed
     */
    public static function set_wx_template($template)
    {
        try
        {
            $rows = DB::table("wx_template")->where(array("template_key" => $template["template_key"]))->update($template);
            return $rows;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }

    /**
     * 设置行业
     * @return mixed
     */
    public static function set_industry()
    {
        try {
            $app    = app("wechat");
            $notice = $app->notice;
            return $notice->setIndustry(1, 17);
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }

    /**
     * 手动单条更新模板
     * @param $id
     * @return bool
     */
    public static function add_template($tmp_id, $id)
    {
        try
        {
            $app    = app("wechat");
            $notice = $app->notice;
            if (!empty($tmp_id)) {
                $notice->deletePrivateTemplate($tmp_id);
            }
            $template = $notice->addTemplate($id);
            $template = json_decode($template, true);
            if (intval($template["errcode"]) != 0) {
                return "false";
            } else {
                $rows = DB::table("wx_template")->where(array("template_key" => $id))->update(array("template_id" => $template["template_id"]));
                if ($rows === false) {
                    return "false";
                } else {
                    return $template["template_id"];
                }
            }
        } catch (\Exception $e) {
            Log::info($e);
            return "false";
        }
    }

    /**
     * 自动更新模板
     * @return bool
     */
    public static function get_private_templates()
    {
        $app       = app("wechat");
        $notice    = $app->notice;
        $templates = $notice->getPrivateTemplates();
        DB::beginTransaction();
        try {
            foreach ($templates["template_list"] as $key => $value) {
                DB::table("wx_template")->where(array("wx_name" => $value["title"]))->update(array("template_id" => $value["template_id"]));
            }
            DB::commit();
            return Weixin::get_wx_template();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            return array();
        }
    }

    /**
     * 获取自动回复消息
     * @return mixed
     */
    public static function get_reply()
    {
        try
        {
            $data = DB::table("wx_autoreply")->where(array("status" => 0))->orderBy('create_time', 'desc')->get();
            return $data;
        } catch (\Exception $e) {
            Log::info($e);
            return [];
        }
    }

    /**
     * 设置关注自动回复
     * @param $data
     * @return mixed
     */
    public static function set_follow($data)
    {
        try
        {
            if ($data["id"] > 0) {
                $rows = DB::table("wx_autoreply")->where(array("id" => $data["id"], "category" => 0))->update(array("content" => $data["content"], "update_time" => time()));
            } else {
                $rows = DB::table("wx_autoreply")->insert(array("content" => $data["content"], "title" => "关注自动回复", "category" => 0, "type" => 1, "event" => "", "status" => 0, "create_time" => time(), "update_time" => time()));
            }
            return $rows;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }

    /**
     * 设置消息自动回复
     * @param $data
     * @return mixed
     */
    public static function set_news($data)
    {
        try
        {
            if ($data["id"] > 0) {
                $rows = DB::table("wx_autoreply")->where(array("id" => $data["id"], "category" => 1))->update(array("content" => $data["content"], "update_time" => time()));
            } else {
                $rows = DB::table("wx_autoreply")->insert(array("content" => $data["content"], "title" => "消息自动回复", "category" => 1, "type" => 1, "event" => "", "status" => 0, "create_time" => time(), "update_time" => time()));
            }
            return $rows;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }

    /**
     * 获取自动回复关键字
     * @param $reply_id
     * @return mixed
     */
    public static function get_words($reply_id)
    {
        try
        {
            $rows = DB::table("wx_keyword")->where(array("reply_id" => $reply_id))->pluck("name");
            return $rows;
        } catch (\Exception $e) {
            Log::info($e);
            return [];
        }
    }

    /**
     * 设置关键字自动回复
     * @param $data
     * @return bool
     */
    public static function set_reply($data)
    {
        DB::beginTransaction();
        try {
            $model = DB::table('wx_autoreply')->where(array("title" => $data["title"]))->where("id", "<>", $data["id"])->first();
            if ($model) {
                DB::rollback();
                return 2;
            }
            DB::table("wx_autoreply")->where(array("id" => $data["id"]))
                ->update(array("title" => $data["title"], "content" => $data["content"], "update_time" => time()));
            DB::table("wx_keyword")->where(array("reply_id" => $data["id"]))->delete();
            foreach ($data["words"] as $item) {
                $model = DB::table('wx_keyword')->where(array("name" => $item))->first();
                if ($model) {
                    DB::rollback();
                    return 3;
                }
                DB::table("wx_keyword")->insert(array("name" => $item, "reply_id" => $data["id"], "create_time" => time()));
            }
            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return 1;
        }
    }

    /**
     * 根据ID获取关键字回复内容
     * @param $id
     */
    public static function get_reply_byid($id)
    {
        try
        {
            $model = DB::table('wx_autoreply')->where(array("id" => $id))->first();
            return $model;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 新增关键字自动回复
     * @param $data
     * @return bool
     */
    public static function create_reply($data)
    {
        DB::beginTransaction();
        try {
            $model = DB::table('wx_autoreply')->where(array("title" => $data["title"]))->first();
            if ($model) {
                DB::rollback();
                return -2;
            }
            $id = DB::table("wx_autoreply")->insertGetId(array("title" => $data["title"], "category" => 2, "type" => 1, "event" => "", "content" => $data["content"], "status" => 0, "create_time" => time(), "update_time" => time()));
            foreach ($data["words"] as $item) {
                $model = DB::table('wx_keyword')->where(array("name" => $item))->first();
                if ($model) {
                    DB::rollback();
                    return -3;
                }
                DB::table("wx_keyword")->insert(array("name" => $item, "reply_id" => $id, "create_time" => time()));
            }

            DB::commit();
            return $id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return -1;
        }
    }

    /**
     * 删除关键字规则
     * @param $id
     * @return bool
     */
    public static function delete_reply($id)
    {
        DB::beginTransaction();
        try {
            DB::table("wx_keyword")->where("reply_id", "=", $id)->delete();
            DB::table("wx_autoreply")->where("id", "=", $id)->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return false;
        }
    }

    /**
     * 获取微信菜单
     * @return array
     */
    public static function get_menu()
    {
        try
        {
            $app       = app("wechat");
            $menu      = $app->menu;
            $menus     = $menu->current();
            $menus_arr = $menus->selfmenu_info['button'];

            /*$menus_arr = array(
            array("id" => 0, "name" => "微直播", "sub_button" => array("list" => array(array("id" => 3,"type" => "view", "name" => "任力的直播", "url" => "http: //www.baidu.com/")))),
            array("id" => 1,"name" => "微官网", "sub_button" => array("list" => array(array("id" => 5,"type" => "view", "name" => "微百科", "url" => "http://www.baidu.com/")))),
            array("id" => 2,"name" => "微官网2", "sub_button" => array("list" => array(array("id" => 4,"type" => "view", "name" => "微百科2", "url" => "http://www.baidu.com/")))),
            );*/
            return $menus_arr;
        } catch (\Exception $e) {
            Log::info($e);
            return [];
        }
    }

    /**
     * 设置微信菜单
     * @param $buttons
     * @return bool
     */
    public static function set_menu($buttons)
    {
        try {
            $app  = app("wechat");
            $menu = $app->menu;
            $menu->destroy();
            $menu->add($buttons);
            return true;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }
}
