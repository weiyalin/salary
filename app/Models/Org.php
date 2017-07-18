<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Org extends Model
{
    // 组织机构

    public $table = 'org';
    public $timestamps = false;

    /**
     * 获取院系
     * @param int $parent_id 默认为顶级院系
     * @return mixed
     */
    public static function get_department($parent_id = 0)
    {
        // 获取所有院系
        $query = DB::table('org')
            ->where('parent_id', (int)$parent_id)
            ->where(function ($query) {
                // 学校直接子结点可有院和系两种
                $query->where('type', 1)->orWhere('type', 2);
            })
            ->where('is_delete', 0)
            ->orderBy('sort_factor', 'desc')
            ->orderBy('name_quanpin', 'asc')
            ->get();

        return $query;
    }

    /**
     * 获取班级
     * @param int $depart_id 默认为0,代表取所有班级
     * @return mixed
     */
    public static function get_class($depart_id = 0)
    {
        // 获取指定系下的班级
        $query = DB::table('org');
        if ($depart_id != 0) {
            $query->where('parent_id', $depart_id);
        }

        $query->where('type', 3)
            ->where('is_delete', 0)
            ->orderBy('sort_factor', 'desc')
            ->orderBy('name_quanpin', 'asc');

        return $query->get();
    }

    public static function get_org($org_id)
    {
        $query = DB::table('org')
            ->where('id', $org_id)
            ->first();

        return $query;
    }

    /**
     * 获取组织机构列表
     * @param int $parent_id 默认为0,代表顶级所有机构
     * @return mixed
     */
    public static function get_org_list($parent_id = 0)
    {
        // 获取所有院系
        $query = DB::table('org')
            ->where('parent_id', $parent_id)
            ->where('is_delete', 0)
            ->orderBy('sort_factor', 'desc')
            ->orderBy('name_quanpin', 'asc')
            ->get();

        return $query;
    }


    public static function save_org($org)
    {
        if ($org['parent_id']) {
            //更改父级属性 -id-id-id-
            $query = DB::table('org')->where('id', $org['parent_id'])->first();
            if ($query) {
                $path = $query->path . $query->id . '-';
            } else {
                $org['parent_id'] = 0;
                $path = '-0-';
            }
        } else {
            $org['parent_id'] = 0;
            $path = '-0-';
        }

        $org['path'] = $path;

        if (empty($org['id'])) {
            //add
            DB::table('org')->insert([
                'name' => $org['name'],
                'name_quanpin' => $org['name_quanpin'],
                'name_jianpin' => $org['name_jianpin'],
                'type' => $org['type'],
                'path' => $org['path'],
                'parent_id' => $org['parent_id'],
                'sort_factor' => intval($org['sort_factor']),
                'create_time' => $org['operate_time'],
                'create_user_id' => $org['operate_user_id']
            ]);
        } else {
            //edit
            DB::table('org')->where('id', $org['id'])->update([
                'name' => $org['name'],
                'name_quanpin' => $org['name_quanpin'],
                'name_jianpin' => $org['name_jianpin'],
                'type' => $org['type'],
                'path' => $org['path'],
                'parent_id' => $org['parent_id'],
                'sort_factor' => intval($org['sort_factor']),
                'update_time' => $org['operate_time'],
                'update_user_id' => $org['operate_user_id']
            ]);
        }
        //更改父级结点属性
        DB::table('org')->where('id', $org['parent_id'])->update(['is_leaf' => 0]);
        return true;
    }

    static function delete_org($id)
    {
        $query = DB::table('org')->where('id', $id)->first();
        if ($query) {
            //删除本身及其后代结点
            DB::table('org')->where('path', 'like', $query->path . $query->id . '-%')->orWhere('id', $id)->update(['is_delete' => 1, 'delete_at' => millisecond()]);

            $rlt = DB::table('org')->where('parent_id', $query->parent_id)->where('is_delete', 0)->count();
            if ($rlt == 0) {//如果父结点没有可用子结点,修改其is_leaf属性值
                DB::table('org')->where('id', $query->parent_id)->update(['is_leaf' => 1]);
            }
        }
        return true;
    }

    static function valid_type($type)
    {
        //机构类型（0：普通单位/部门，1：院，2：系，3：班级）
        return in_array($type, [0, 1, 2, 3]);
    }


    /**
     * 获取指定节点及其所有后代节点ID
     * @param mixed $orgIds
     * @param bool $includeSelf 是否包含指定节点本身
     * @return mixed
     */
    static function get_org_ids($orgIds, $includeSelf = true)
    {
        $query = DB::table('org');
        if (is_array($orgIds)) {
        } else {
            $orgIds[] = intval($orgIds);
        }
        $rlt = DB::table('org')->whereIn('id', $orgIds)->get();
        if ($rlt) {
            $query->where(function ($query) use ($rlt) {
                foreach ($rlt as $key => $val) {
                    $query->orWhere('path', 'like', $val->path . $val->id . '-%');
                }
            });
        } else {
            //给定节点不合法
            return [];
        }

        $rltIds = $query->pluck('id')->toArray();
        if ($includeSelf) {
            $rltIds = array_merge($orgIds, $rltIds);
        }

        //去重复
        $rltIds = array_flip($rltIds);
        $rltIds = array_flip($rltIds);

        return $rltIds;
    }
}
