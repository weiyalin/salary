<?php

namespace App\Http\Controllers\User;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Overtrue\LaravelPinyin\Facades\Pinyin;
use Excel;

class StudentController extends Controller
{
    //
    function get(){
        $id = intval(Input::get('id'));
        return Student::get($id);
    }

    function lists(){
        $per_page = intval(Input::get('per_page'));
        $keyword = Input::get('keyword');
        return Student::lists($keyword,$per_page);
    }

    function edit(){
        $id = intval(Input::get('id'));
        $name = Input::get('name');
        $code = Input::get('code');
        $mobile = Input::get('mobile');
        $id_card = Input::get('id_card');
        $sex = Input::get('sex');
        $class_id = intval(Input::get('class_id'));
        $class_name = Input::get('class_name');
        $major_id = intval(Input::get('major_id'));
        $major_name = Input::get('major_name');
        $department_id = intval(Input::get('department_id'));
        $department_name = Input::get('department_name');

        if($name == false){
            return responseToJson(1,'名称不能为空');
        }

        if($code == false){
            return responseToJson(1,'学号不能为空');
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
            'class_id'=>$class_id,
            'class_name'=>$class_name,
            'major_id'=>$major_id,
            'major_name'=>$major_name,
            'department_id'=>$department_id,
            'department_name'=>$department_name,
            'update_time'=>$now
        ];
        if($id == false){
            $entity['create_time']=$now;
        }

        return Student::edit($id,$entity);

    }

    function delete(){
        $id = intval(Input::get('id'));
        return Student::del($id);
    }

    function reset(){
        $id = intval(Input::get('id'));
        return Student::reset($id);
    }


    function import(Request $request){
        return Student::import($request);
    }

    /**
     * 学生模板
     */
    public function template(){
        Excel::create("学生模板",function ($excel){
            $excel->sheet("学生模板",function ($sheet){
                $data=[
                    ['姓名','学号','性别','手机号','身份证号','院系(层级之间用 / 分隔)','专业','班级'],
                ];
                $sheet->rows($data);

                $sheet->setWidth(array(
                    'A'     => 15,
                    'B'     => 15,
                    'C'     => 15,
                    'D'     => 15,
                    'E'     => 15,
                    'F'     => 30,
                    'G'     => 15,
                    'H'     =>15

                ));
            });
        })->export("xls");
    }



}
