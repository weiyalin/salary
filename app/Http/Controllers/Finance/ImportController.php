<?php

namespace App\Http\Controllers\Finance;

use App\Models\Salary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Excel;
use Illuminate\Support\Facades\Input;

class ImportController extends Controller
{
    //
    function templates(){
        $list = DB::table('template')->select('id','name')->get();
        return responseToJson(0,'success',$list);
    }

    function import(){
        $yearMonth = Input::get('yearMonth');
        $name = Input::get('name');
        $template_id = Input::get('template_id');
        $password = Input::get('password');
        if($password){
            $password = generate_salary_password($password);
        }
        $file = Input::get('file');

        $reader = Excel::load($file);
        $reader = $reader->getSheet(0);

        $results = $reader->toArray();
        if (empty($results) || count($results)<=1) {
            return responseToJson(1,'工资表内容为空');
        }

        $date_arr = explode("-",$yearMonth);

        $code_index = -1;
        $name_index = -1;
        for($j=0;$j<count($results[0]);$j++){
            if($results[0][$j] == '工号'){
                $code_index = $j;
            }
            if($results[0][$j] == '姓名'){
                $name_index = $j;
            }
        }

        if($code_index == -1 ){
            $code_index = 0;
        }
        if($name_index == -1){
            $name_index = 1;
        }



        //copy template
        $template = DB::table('template')->where('id',$template_id)->first();

        $template = (array)$template;
        $template['template_id'] = $template['id'];
        unset($template['id']);
        $snapshot_id = DB::table('template_snapshot')->insertGetId($template);

        //构造salary
        $salary = [
            'name'=>$name,
            'password'=>$password,
            'send_title'=>$yearMonth,
            'send_year'=>intval($date_arr[0]),
            'send_month'=>intval($date_arr[1]),
            'tpl_snapshot_id'=>$snapshot_id,
            'total_person'=>count($results)-1,
            'create_user_id'=>get_user_id(),
            'create_user_name'=>get_user_info()->name,
            'create_time'=>millisecond(),
        ];

        $salary_id = DB::table('salary')->insertGetId($salary);

        $error_data = array();
        $result_arr = array();

        $details = [];
        //遍历解析数据
        for($i=1;$i<count($results);$i++){
            $data = [];

            $tmp_code = $results[$i][$code_index];
            $tmp_name = $results[$i][$name_index];
            $user = DB::table('user')->where('code',$tmp_code)->first();
            if($user == false){
                $content = implode(",",$results[$i]);
                $tmp = ['code'=>$tmp_code,'name'=>$tmp_name,'content'=>$content];
                $tmp['error']='工号不存在';
                $error_data[] = $tmp;
                continue;
            }
            if(env('Is_Valid_Name') == 1){
                if($user->name != trim($tmp_name)){
                    $content = implode(",",$results[$i]);
                    $tmp = ['code'=>$tmp_code,'name'=>$tmp_name,'content'=>$content];
                    $tmp['error']='工号姓名不匹配';
                    $error_data[] = $tmp;
                    continue;
                }
            }



            $data['code'] = $results[$i][$code_index];
            $data['salary_id']= $salary_id;
            $data['send_title']=$yearMonth;
            $data['tpl_snapshot_id']=$snapshot_id;
            $data['create_user_name']=get_user_info()->name;
            $data['create_user_id']=get_user_id();
            $data['create_time']=millisecond();
            $data['send_year'] = intval($date_arr[0]);
            $data['send_month'] = intval($date_arr[1]);


            for($j=0;$j<count($results[$i]);$j++){
                $str = 'c'.($j+1);
                $data[$str] = $results[$i][$j];
            }

            $details[] = $data;
        }

        //inserts

        if(count($details) > 0){
            DB::table('salary_detail')->insert($details);
            $count = count($details);
            DB::table('salary')->where('id',$salary_id)->update(['total_person'=>$count]);
        }
        else {
            DB::table('salary')->where('id',$salary_id)->delete();
            $count = 0;
        }

        if($password){
            Salary::cache_salary_password($salary_id);
        }

        $error = false;
        if(count($error_data) > 0){
            $error = true;
        }

        $result_arr = ['id'=>$salary_id,'name'=>$name,'error'=>$error,'error_data'=>$error_data,'success_count'=>$count];


        return responseToJson(0,'上传成功',$result_arr);

    }


    function import_file(Request $request){
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

        return responseToJson(0,'success',$filePath);

    }

}
