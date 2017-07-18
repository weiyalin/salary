<?php

namespace App\Http\Controllers\Finance;

use DB;
use Excel;
use App\Models\QyWechat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use PhpOffice\PhpWord\Style\Paragraph;

class MemberController extends Controller
{
    //
    function lists(Request $request)
    {
        $pageSize = $request->page_size;
        $keyword = trim($request->keyword);
        $status = intval($request->status);
        $department = intval($request->department);
        $query = DB::table('user');

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($department) {
            $children = $this->org2($department);
            $children[] = $department;

            if (get_user_info()->is_super_manager == false) {
                $manage = DB::table('user')->where('id', get_user_id())->value('manage_department');
                $arr = explode(",", $manage);
                $nodes = $this->get_nodes($arr);
                //dd($nodes);
                $children =  array_intersect($children,$nodes);
            }


            $user_id_list = DB::table('user_org')->whereIn('org_id', $children)->pluck('user_id');
            $query->whereIn('id', $user_id_list);
        } else {
            if (get_user_info()->is_super_manager == false) {
                $manage = DB::table('user')->where('id', get_user_id())->value('manage_department');
                $arr = explode(",", $manage);
                $children = $this->get_nodes($arr);
//                foreach ($arr as $depart_id) {
//                    $list = $this->org2($depart_id);
//                    $children = array_collapse([$children, $list]);
//                }
//                $children[] = array_collapse([$children, $arr]);


                $user_id_list = DB::table('user_org')->whereIn('org_id', $children)->pluck('user_id');
                $query->whereIn('id', $user_id_list);
            }
        }

        return responseToJson(0, 'success', ['is_super' => get_user_info()->is_super_manager, 'table' => $query->paginate($pageSize)]);

    }

    function export(Request $request)
    {
        $keyword = trim($request->keyword);
        $status = intval($request->status);
        $department = intval($request->department);
        $query = DB::table('user');

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($department) {

            $children = $this->org2($department);
            $children[] = $department;

            if (get_user_info()->is_super_manager == false) {

                $manage = DB::table('user')->where('id', get_user_id())->value('manage_department');
                $arr = explode(",", $manage);
                $nodes = $this->get_nodes($arr);
                //dd($nodes);
                $children =  array_intersect($children,$nodes);
            }


            $user_id_list = DB::table('user_org')->whereIn('org_id', $children)->pluck('user_id');
            $query->whereIn('id', $user_id_list);
        } else {
            if (get_user_info()->is_super_manager == false) {
                $manage = DB::table('user')->where('id', get_user_id())->value('manage_department');
                $arr = explode(",", $manage);
                $children = $this->get_nodes($arr);
                //dd($children);
//                foreach ($arr as $depart_id) {
//                    $list = $this->org2($depart_id);
//                    $children = array_collapse([$children, $list]);
//                }
//                $children[] = array_collapse([$children, $arr]);


                $user_id_list = DB::table('user_org')->whereIn('org_id', $children)->pluck('user_id');
                $query->whereIn('id', $user_id_list);
            }
        }

        $data = $query->get();

        Excel::create('员工信息', function ($excel) use ($data) {
            $excel->sheet('员工信息', function ($sheet) use ($data) {
                $output = [[
                    '工号',
                    '姓名'
                ]];

                foreach ($data as $key => $value) {
                    $output[] = [
                        $value->code,
                        $value->name
                    ];
                }

                $sheet->rows($output);
            });
        })->download('xls');
    }

    function get_manage()
    {
        $id = Input::get('id');
        if (get_user_info()->is_super_manager == false) {
            return responseToJson(1, '您无权操作,请联系管理员');
//            $arr = explode(",",$manage_department);
//            //当前用户管理的部门
//            $children = [];
//            foreach($arr as $depart_id){
//                $list = $this->org2($depart_id);
//                $children = array_collapse([$children,$list]);
//            }
//            $depart_list = DB::table('org')->where('parent_id','>',0)->whereIn('id',$children)->select('id','name')->get();

        }

        //$depart_list = DB::table('org')->where('parent_id', '>', 0)->select('id', 'name')->get();
        $tree = $this->tree2();


        $user = DB::table('user')->where('id', $id)->first();
        $manage_department = $user->manage_department;

        $select_list = explode(",",$manage_department);

        return responseToJson(0,'success',['tree'=>$tree,'select_list'=>$select_list,'is_grade_manager' => $user->is_grade_manager, 'mobile' => $user->mobile]);


//        $values = [];
//        $orgs = [];
//        if ($manage_department) {
//            $arr = explode(",", $manage_department);
//            foreach ($depart_list as $item) {
//                if (in_array($item->id, $arr)) {
//                    $values[] = $item->id;
//                }
//            }
//        }
//
//        foreach ($depart_list as $item) {
//            $orgs[] = ['id' => $item->id, 'name' => $item->name];
//        }
        //return responseToJson(0, 'success', ['values' => $values, 'orgs' => $orgs, 'is_grade_manager' => $user->is_grade_manager, 'mobile' => $user->mobile]);
    }

    function save_manage()
    {
        $values = Input::get('values');
        $id = Input::get("id");
        $password = Input::get('password');
        $mobile = Input::get('mobile');

        if ($values) {
            if ($password == false) {
                $user = DB::table('user')->where('id', $id)->first();
                if ($user->is_grade_manager == false) {
                    return responseToJson(1, '密码不能为空');
                }
                //不需要修改密码
            } else {
                //需要更新密码
                $salt = get_salt();
                $pwd = get_md5_password($password, $salt);
                $updateData['salt'] = $salt;
                $updateData['password'] = $pwd;
            }
            $str = implode(",", $values);
            $updateData['manage_department'] = $str;
            $updateData['role_id'] = '10';
            $updateData['is_grade_manager'] = 1;
            $updateData['mobile'] = $mobile;
            DB::table('user')->where('id', $id)->update($updateData);
        } else {
            DB::table('user')->where('id', $id)->update(['manage_department' => "", 'role_id' => '', 'is_grade_manager' => 0, 'salt' => '', 'password' => '']);
        }
        return responseToJson(0, '设置成功', '设置成功');
    }

    function sync()
    {
        return QyWechat::sync();
    }

    function options()
    {

        $query = DB::table('user');
        if (get_user_info()->is_super_manager == false) {
            $manage_department = DB::table('user')->where('id', get_user_id())->value('manage_department');
            if ($manage_department) {
                $arr = explode(",", $manage_department);
                $children = $this->get_nodes($arr);
//                $children = $this->org3($arr);
//                $children = array_collapse([$children, $arr]);

                $user_id_list = DB::table('user_org')->whereIn('org_id', $children)->pluck('user_id');
                $query->whereIn('id', $user_id_list);

            } else {
                return [];
            }
        }

        $data = [];

        $count = $query->count();
        $data[] = ['id' => 0, 'name' => '全部(' . $count . ')'];

        $count_1 = $query->where('status', 1)->count();
        $data[] = ['id' => 1, 'name' => '已关注(' . $count_1 . ')'];

        $count_2 = $query->where('status', 2)->count();
        $data[] = ['id' => 2, 'name' => '已禁用(' . $count_2 . ')'];

        $count_4 = $query->where('status', 4)->count();
        $data[] = ['id' => 4, 'name' => '未关注(' . $count_4 . ')'];

        return responseToJson(0, 'success', $data);

    }

    function tree()
    {
        $result = [];
        if (get_user_info()->is_super_manager == false) {
            $manage_department = DB::table('user')->where('id', get_user_id())->value('manage_department');
            if ($manage_department) {
                $arr = explode(",", $manage_department);
                $result = $this->tree3($arr);

//                $lists = DB::table('org')->whereIn('id', $arr)->get();
//                foreach ($lists as $item) {
//                    $result[] = [
//                        'id' => $item->id,
//                        'label' => $item->name,
//                        'children' => []
//                    ];
//                }
            }
        } else {
            $result = $this->tree2();
        }

        return responseToJson(0, 'success', $result);

    }

    private function get_nodes($nodes){
        $paths = DB::table('org')->whereIn('id',$nodes)->pluck('path');
        $list = $nodes;
        foreach($paths as $path){
            $arr = explode("-",$path);
            $list = array_collapse([$list,$arr]);
        }

        $list = array_flip(array_flip($list));
        return $list;
    }

    private function tree3($nodes)
    {
        $list = $this->get_nodes($nodes);
        $tree = $this->tree2(0,$list);

//        $tree = [];
//        $data = DB::table('org')->whereIn('parent_id', $parent_list)->get();
//        if ($data) {
//            foreach ($data as $val) {
//                $id = $val->id;
//                $name = $val->name;
//                $children = $this->tree2($id);
//                $tree[] = [
//                    'id' => $id,
//                    'label' => $name,
//                    'children' => $children
//                ];
//            }
//        }

        return $tree;
    }

    private function tree2($parent_id = 0,$limits = null)
    {
        if($parent_id == 0){
            $parent_id = DB::table('org')->min('parent_id');
        }
        $tree = [];
        if($limits){
            $data = DB::table('org')->where('parent_id', $parent_id)->whereIn('id',$limits)->get();
        }
        else {
            $data = DB::table('org')->where('parent_id', $parent_id)->get();
        }

        if ($data) {
            foreach ($data as $val) {
                $id = $val->id;
                $name = $val->name;
                $children = $this->tree2($id,$limits);
                $tree[] = [
                    'id' => $id,
                    'label' => $name,
                    'children' => $children
                ];
            }
        }

        return $tree;
    }

    private function org2($parent_id = 0)
    {
        // $id_list = [];
        $str = "%-" . $parent_id . "-%";
        $id_list = DB::table('org')->where('path', 'like', $str)->pluck('id')->toArray();
//        if($data){
//            foreach($data as $val){
//                $id = $val->id;
//                $children = $this->tree2($id);
//                $id_list[] = $id;
//                foreach($children as $item){
//                    $id_list[] = $item;
//                }
//
//            }
//        }

        return $id_list;
    }

    private function org3($parent_list)
    {
        $id_list = [];

        foreach ($parent_list as $id) {
            $str = "%-" . $id . "-%";
            $arr = DB::table('org')->where('path', 'like', $str)->pluck('id')->toArray();
            $id_list = array_collapse([$id_list, $arr]);
        }

//        $data = DB::table('org')->whereIn('parent_id',$parent_list)->get();
//        if($data){
//            foreach($data as $val){
//                $id = $val->id;
//                $children = $this->tree2($id);
//                $id_list[] = $id;
//                foreach($children as $item){
//                    $id_list[] = $item;
//                }
//
//            }
//        }

        return $id_list;
    }

    function clear()
    {
        $id = Input::get('id');
        DB::table('user')->where('id', $id)->update(['salary_password' => '']);

        return responseToJson(0, '清空密码成功', '清空密码成功');
    }

}
