<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Excel;
use Illuminate\Support\Facades\Input;

class TemplateController extends Controller
{
    //模板列表
    function lists(){
        $templates = DB::table('template')->get();
        $list = [];
        foreach($templates as $tpl){
            $id = $tpl->id;
            $name = $tpl->name;
            $url = '/salary/template_import?id='.$id;
            unset($tpl->id);
            unset($tpl->name);
            $list [] = ['id'=>$id,'name'=>$name,'data'=>[$tpl],'url'=>$url];
        }

        return responseToJson(0,'success',$list);
    }

    //保存模板
    function save(){
        $templates = Input::get('templates');

        $insert_list = [];
        foreach($templates as $tpl){
            $id = $tpl['id'];
            $name = $tpl['name'];
            $data = $tpl['data'][0];
            //dd($data);
            if($id == 0){

                unset($data['id']);
                $data['name'] = $name;
                $insert_list[] = $data;
            }
            else {
                unset($data['id']);
                DB::table('template')->where('id',$id)->update($data);
            }
        }
        if($insert_list){
            DB::table('template')->insert($insert_list);
        }


        return responseToJson(0,'保存成功','保存成功');
    }

    function del(){
        $id = Input::get('id');
        DB::table('template')->where('id',$id)->delete();
        return responseToJson(0,'删除成功','删除成功');
    }


    //导入模板
    function import(Request $request){
        $id = Input::get('id');
        if($id == false){
            return responseToJson(2,'id不能为空');
        }
        //判断请求中是否包含name=file的上传文件
        if (!$request->hasFile('files')) {
            return responseToJson(1, '上传文件为空！');
        }
        $file = $request->file('files');
        //判断文件上传过程中是否出错
        if (!$file->isValid()) {
            return responseToJson(2, '文件上传出错！');
        }

        $destPath = realpath(storage_path('app/tmp'));
        if (!file_exists($destPath))
            mkdir($destPath, 0777, true);
        //$filename = $file->getClientOriginalName();
        $filename = get_user_id() . 'and' . millisecond() . '.xls';
        if ($file->move($destPath, $filename) == false) {
            return responseToJson(3, '保存文件失败！');
        }


        //excel导入
        $filePath = 'storage/app/tmp/' . $filename;

        $reader = Excel::load($filePath);
        $reader = $reader->getSheet(0);

        $results = $reader->toArray();
        if (empty($results) || count($results)<=0) {
            return responseToJson(1,'工资表内容为空');
        }

        $arr = $results[0];
        $data = [];
        for($j=0;$j<count($arr);$j++){
            $field = 'c'.($j + 1);
            $data[$field] = $arr[$j];
        }

        DB::table('template')->where('id',$id)->update($data);

        return responseToJson(0,'导入成功','导入成功');

    }

    //下载模板
    function download(){
        $id = Input::get('id');
        $template = DB::table('template')->where('id',$id)->first();
        $name = filter_filename($template->name);
        Excel::create($name,function ($excel) use($template) {
            $excel->sheet("工资条样板",function ($sheet) use($template){
                $item = [];
                for($i=1;$i<=50;$i++){
                    $t = 'c'.$i;
                    $item[] = $template->$t;
                }
//                $data=[
//                    ['姓名','学号','性别','手机号','身份证号','院系(层级之间用 / 分隔)','专业','班级'],
//                ];
                $data = [$item];
                $sheet->rows($data);

                $sheet->setWidth(array(
                    'A'     => 15,
                    'B'     => 15,
                    'C'     => 15,
                    'D'     => 15,
                    'E'     => 15,
                    'F'     => 15,
                    'G'     => 15,
                    'H'     =>15

                ));
            });
        })->export("xls");

    }


}
