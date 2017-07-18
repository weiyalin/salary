<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Role;
use Log;

class User extends Model
{
    const TYPE_STUDENT = 1;
    const TYPE_TEACHER = 2;
    const TYPE_UNKNOWN = 0;

    public $table = 'user';
    public $timestamps = false;

    //
    static function get_user_list_paginate($page, $pageSize)
    {
        $user = DB::table('user')->where('status', 0);
        $total = $user->count();
        $users = $user
            ->select('user.*')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->orderBy('id', 'desc')
            ->get();
        return ['total' => $total, 'users' => $users];
    }

    static function get_user_info($id)
    {
        $info = DB::table('user')->where('id', $id)->where('status', 0)->select('id', 'name', 'phone', 'role_id')->first();
        $roles = Role::get_role_list();
        return ['info' => $info, 'roles' => $roles];
    }

    static function reset_pwd($id)
    {
        $salt = str_random(4);
        $password = self::encrypt_pwd('123456', $salt);
        return DB::table('user')
            ->where('id', $id)
            ->update(['password' => $password, 'salt' => $salt, 'update_time' => millisecond()]);
    }

    static function is_unique_name($name, $exceptId = 0)
    {
        return DB::table('user')
            ->where('name', $name)
            ->where('id', '!=', $exceptId)
            ->where('status', 0)
            ->first(['id']) ? false : true;
    }

    static function save_user($id = 0, $data)
    {
        if ($id) {
            $data['update_time'] = millisecond();
            return DB::table('user')->where('id', $id)->update($data);
        } else {
            $data['salt'] = str_random(4);
            $data['password'] = self::encrypt_pwd('123456', $data['salt']);
            $data['create_time'] = millisecond();
            return DB::table('user')->insert($data);
        }
    }


    static function soft_delete($id)
    {
        return DB::table('user')->where('id', $id)->update(['status' => 1, 'update_time' => millisecond()]);
    }

    static function is_password_right($pwd)
    {
        $user = DB::table('user')->where('id', get_user_id())->first();
        return $user->password == md5(md5($pwd) . $user->salt);
    }

    static function change_password($new_pwd)
    {
        $salt = str_random(4);
        $password = md5(md5($new_pwd) . $salt);
        $res = DB::table('user')
            ->where('id', get_user_id())
            ->update(['password' => $password, 'salt' => $salt]);
        return $res;
    }

    protected static function encrypt_pwd($originalPwd, $salt)
    {
        return md5(md5($originalPwd) . $salt);
    }


    public static function history($arr)
    {
        DB::table('login_history')->insert($arr);
        return true;
    }

    /**
     * 获取用户信息
     * @param  [String] $code or $phone
     * @return  Object
     */
    public static function get_user_by_code($codeOrMobile)
    {
        try {
            $user = DB::table("user")->where("code", $codeOrMobile)->orWhere('mobile', $codeOrMobile)->first();
            return $user;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    public static function get_user_list($depart_id, $type)
    {
        $org = DB::table('org')->where('id', $depart_id)->first();
        if (!$org) {
            return [];
        }
        //获取depart_id下级机构
        $orgList = DB::table('org')->where('path', 'like', $org->path . $org->id . '-%')->where('is_delete', 0)->pluck('id');

        if (!$orgList) {
            $orgList = [];
        }
        $orgList[] = $org->id;

        $query = DB::table('user')
            ->whereIn('org_id', $orgList)
            ->where('type', $type)
            ->where('status', 0)
            ->get();
        return $query;
    }
}
