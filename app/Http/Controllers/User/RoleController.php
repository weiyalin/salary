<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Response;
use App\Models\Menu;

class RoleController extends Controller
{
    //
    function get_role_list_paginate(Request $request){
        return Role::get_role_list_paginate($request->page,$request->pageSize);
    }

    function edit(Request $request){
        $roleId = $request->id;
        $menu = Menu::get_format_menu();
        $info = [];
        $role_code = null;
        if($roleId){
            $info = Role::get_role_info($roleId);   //获取角色名称
            $role_code = Role::get_role_menu($roleId);  //获取角色拥有的权限菜单
        }
        return Response::json(['menu'=>$menu,'info'=>$info,'role_code'=>$role_code]);
    }

    function edit_save(Request $request){
        $id = intval($request->id);
        if($id && Role::is_built_in($id)){
            return Response::json(['status'=>1,'msg'=>'内置角色无法编辑']);
        }
        $name = $request->name;
        $code = $request->code;
        if(Role::role_save($id,$name,$code)){
            return Response::json(['status' => 0, 'msg' => '保存成功']);
        }else{
            return Response::json(['status'=>1,'msg'=>'保存失败，请重试']);
        }
    }

    function delete(Request $request){
        $roleId = $request->id;

        if(Role::is_built_in($roleId)){    //是否为内置
            return Response::json(['status'=>1,'msg'=>'内置角色无法删除']);
        }

        if(Role::has_user($roleId)){
           return Response::json(['status'=>1,'msg'=>'此角色下拥有用户，暂时无法删除']);
        }

        if(Role::delete_role($roleId)) {
            return Response::json(['status' => 0, 'msg' => '删除成功']);
        }else{
            return Response::json(['status'=>1,'msg'=>'删除失败']);
        }
    }
}
