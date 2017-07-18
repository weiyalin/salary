<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Role extends Model
{
    static function get_role_list_paginate($page, $pageSize)
    {
        $role = DB::table('role');
        $total = $role->count();
        $roles = $role->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();
        return ['total' => $total, 'roles' => $roles];
    }

    //
    static function get_role_list()
    {
        return DB::table('role')->select('id', 'role_name')->get();
    }

    static function get_role_info($roleId)
    {
        return DB::table('role')->where('id', $roleId)->first(['role_name', 'type']);
    }

    /**
     * 获取角色拥有的权限菜单code
     * @param $roleId [mix] array or int
     * @return mixed
     */
    static function get_role_menu($roleId)
    {
        $table = DB::table('role_node');
        if (is_array($roleId)) {
            $table->whereIn('role_id', $roleId);
        } else {
            $roleId = intval($roleId);
            $table->where('role_id', $roleId);
        }
        return $table->pluck('node_code');
    }

    static function role_save($id = 0, $roleName, $authCode = [])
    {
        DB::beginTransaction();
        try {
            if ($id) {    //更新roleName，并删除所有权限
                DB::table('role')->where('id', $id)->update(['role_name' => $roleName, 'update_time' => millisecond()]);
                DB::table('role_node')->where('role_id', $id)->delete();
            } else {      //插入roleName
                $id = DB::table('role')->insertGetId(['role_name' => $roleName, 'type' => 1, 'create_time' => millisecond()]);
            }
            if ($authCode) {
                foreach ($authCode as $v) {
                    $node[] = ['role_id' => $id, 'node_code' => $v];
                }
                DB::table('role_node')->insert($node);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            //Log::info($e);
            DB::rollback();
            return false;
        }
    }

    static function has_user($roleId)
    {
        return DB::table('user')->where('role_id', $roleId)->first(['id']) ? true : false;
    }

    static function is_built_in($roleId)
    {
        return DB::table('role')->where('id', $roleId)->value('type') == 0 ? true : false;
    }

    static function delete_role($roleId)
    {
        DB::table('role')->where('id', $roleId)->delete();
        DB::table('role_node')->where('role_id', $roleId)->delete();
        return true;
    }

    /**
     * @param $roleId [mix] array or int
     * @return array
     */
    static function get_role_permission($roleId)
    {
        // 1. 获取role_node中角色对应的权限code
        $codes = self::get_role_menu($roleId);
        // 2. 获取code对应的路由以及其下级的所有路由
        $path = [];
        if ($codes) {
            $sql = 'select `path` from node where 1<>1 ';
            foreach ($codes as $v) {
                $sql .= " or `code` like '$v%' ";
            }

            foreach (DB::select($sql) as $v) {
                if (!empty($v->path) && $v->path != '#') {
                    $path[] = $v->path;
                }
            }
        }
        return $path;
    }
}
