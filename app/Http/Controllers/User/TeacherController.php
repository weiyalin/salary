<?php

namespace App\Http\Controllers\User;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Excel;

class TeacherController extends Controller
{
    //
    function get(){
        $id = intval(Input::get('id'));
        return Teacher::get($id);
    }

    function lists(){
        $per_page = intval(Input::get('per_page'));
        $keyword = Input::get('keyword');
        return Teacher::lists($keyword,$per_page);
    }

    function edit(){
        $id = intval(Input::get('id'));
        $name = Input::get('name');
        $code = Input::get('code');
        $mobile = Input::get('mobile');
        $id_card = Input::get('id_card');
        $sex = Input::get('sex');
        $title = Input::get('title');
        $department_id = intval(Input::get('org_id'));
        $department_name = Input::get('org_name');

        if($name == false){
            return responseToJson(1,'名称不能为空');
        }

        if($code == false){
            return responseToJson(1,'工号不能为空');
        }

        $name_quanpin = get_pinyin_all($name);
        $name_jianpin = get_pinyin_simple($name);
        $now = millisecond();

        $entity = [
            'name'=>$name,
            'name_quanpin'=>$name_quanpin,
            'name_jianpin'=>$name_jianpin,
            'code'=>$code,
            'mobile'=>$mobile,
            'id_card'=>$id_card,
            'sex'=>$sex,
            'org_id'=>$department_id,
            'org_name'=>$department_name,
            'title'=>$title,
            'update_time'=>$now
        ];
        if($id == false){
            $entity['create_time']=$now;
        }

        return Teacher::edit($id,$entity);

    }

    function delete(){
        $id = intval(Input::get('id'));
        return Teacher::del($id);
    }

    function reset(){
        $id = intval(Input::get('id'));

        return Teacher::reset($id);
    }


    function import(Request $request){
        return Teacher::import($request);
    }

    /**
     * 教师模板
     */
    public function template(){
        Excel::create("教师模板",function ($excel){
            $excel->sheet("教师模板",function ($sheet){
                $data=[
                    ['姓名','工号','性别','手机号','身份证号','院系(层级之间用 / 分隔)','职务(多个之间用逗号分隔)'],
                ];
                $sheet->rows($data);

                $sheet->setWidth(array(
                    'A'     => 15,
                    'B'     => 15,
                    'C'     => 15,
                    'D'     => 15,
                    'E'     => 15,
                    'F'     => 30,
                    'G'     => 15

                ));
            });
        })->export("xls");
    }


}
