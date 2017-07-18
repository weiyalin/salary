<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class Publish extends Model
{
    /**
     * 发布表单
     * @return JSON
     */
    public static function set_form($id, $uri)
    {
        try {
            $result = DB::table("forms")->where("id", $id)->update([
                "status" => 1,
                "url_code" => $uri,
            ]);
            return $result;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 设置表单是否需要登录
     * @return JSON
     */
    public static function set_form_login($id, $num, $type)
    {
        try {
            $field = "";

            if($type == 1){
                $field = "is_login";
            }elseif($type == 2){
                $field = "is_only";
            }elseif($type == 3){
                $field = "is_wx";
            }

            if($field != ""){
                $result = DB::table("forms")->where("id", $id)->update([
                    $field => $num,
                ]);
                return $result;
            }else{
                return null;
            }
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 获取表单信息
     * @return JSON
     */
    public static function get_form($id)
    {
        try {
            $result = DB::table("forms")->where("id", $id)->first();
            return $result;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 获取表单信息code
     * @return JSON
     */
    public static function get_form_code($code)
    {
        try {
            $result = DB::table("forms")->where("url_code", $code)->first();
            return $result;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 保存微信分享配置
     * @return JSON
     */
    public static function update_wxshare($form, $uri)
    {
        try {
            $result = DB::table("forms")->where("url_code", $uri)->update([
                "share_title" => $form["title"],
                "share_desc" => $form["desc"],
                "share_pic" => $form["img"]
            ]);
            return $result;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    /**
     * 获取微信分享配置
     * @return JSON
     */
    public static function get_wxshare($uri)
    {
        try {
            $result = DB::table("forms")->where("url_code", $uri)->first();
            return $result;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }
}