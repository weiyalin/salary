<?php

namespace App\Http\Controllers\Form;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Log;
use App\Http\Controllers\Controller;
use App\Models\Publish;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\RsaCrypt;

class PublishController extends Controller
{
    /**
     * 队列测试
     * @return JSON
     */
    public function set_test(Request $request)
    {
        $submit = $request->input("submit");
        Log::info($submit);
    }

    /**
     * 发布表单，生成uri
     * @return JSON
     */
    public function set_form(Request $request)
    {
        $id = $request->input("id");
        $uri = substr(md5($id . str_random(4)),8,16);

        $result = Publish::set_form($id, $uri);
        if ($result) {
            return responseToJson(0, "success", $result);
        } else {
            return responseToJson(1, "fail");
        }
    }

    /**
     * 设置表单是否需要登录
     * @return JSON
     */
    public function set_form_login(Request $request)
    {
        $id = $request->input("id");
        $num = $request->input("num");
        $type = $request->input("type");

        $result = Publish::set_form_login($id, $num, $type);
        if ($result) {
            return responseToJson(0, "success", $result);
        } else {
            return responseToJson(1, "fail");
        }
    }

    /**
     * 获取表单uri
     * @return JSON
     */
    public function get_form(Request $request)
    {
        $id = $request->input("id");

        $result = Publish::get_form($id);

        if ($result) {
            $result->enable = env('NOTIFY_ENABLE', 1);
            return responseToJson(0, "success", $result);
        } else {
            return responseToJson(1, "fail");
        }
    }

    /**
     * 获取用户权限下的组织机构和组
     * @return JSON
     */
    public function get_data(Request $request)
    {
        $user_id = get_user_openid();
        $first = $request->input("first");
        $id = $request->input("id");
        $type = $request->input("type");
        $page = $request->input("page");
        $member = $request->input("member");

        $curl = new \anlutro\cURL\cURL();
        $url = env('NOTIFY_URL').'/api/notify/data';
        $rsa_id = RsaCrypt::encode($user_id);
        $response = $curl->post($url,['user'=>$rsa_id, 'code'=>env('AUTH_CODE'), 'first'=>$first, 'id'=>$id, 'type'=>$type, 'page'=>$page, 'member'=>$member]);
        if($response && $response->statusCode == 200){
            $result = json_decode($response->body);
            if($result->code == 0){
                $data = $result->result;
                return Response::json(['status' => 0, 'msg' => $data]);
            } else {
                return Response::json(['status' => 1, 'msg' => "请刷新页面重新尝试"]);
            }
        } else {
            Log::error($response);
            return Response::json(['status' => 1, 'msg' => '网络错误,请稍候尝试']);
        }
    }

    /**
     * 搜索选择接收人
     * @return JSON
     */
    public function get_search(Request $request)
    {
        $user_id = get_user_openid();
        $keyword = $request->input("keyword");

        $curl = new \anlutro\cURL\cURL();
        $url = env('NOTIFY_URL').'/api/notify/search';
        $rsa_id = RsaCrypt::encode($user_id);
        $response = $curl->post($url,['id'=>$rsa_id, 'code'=>env('AUTH_CODE'), 'keyword'=>$keyword]);
        if($response && $response->statusCode == 200){
            $result = json_decode($response->body);
            if($result->code == 0){
                $data = $result->result;
                return Response::json(['status' => 0, 'msg' => $data]);
            } else {
                return Response::json(['status' => 1, 'msg' => "请刷新页面重新尝试"]);
            }
        } else {
            Log::error($response);
            return Response::json(['status' => 1, 'msg' => '网络错误,请稍候尝试']);
        }
    }

    /**
     * 保存微信分享配置
     * @return JSON
     */
    public function update_wxshare(Request $request)
    {
        $form = $request->input("form");
        $uri = $request->input("uri");
        $result = Publish::update_wxshare($form, $uri);

        return responseToJson(0, "success", $result);
    }

    /**
     * 获取微信分享配置
     * @return JSON
     */
    public function get_wxshare(Request $request)
    {
        $uri = $request->input("uri");
        $result = Publish::get_wxshare($uri);
        if ($result) {
            return responseToJson(0, "success", $result);
        } else {
            return responseToJson(1, "fail");
        }
    }

    public function get_img($img_name)
    {
        return Storage::disk('public')->get($img_name);
    }

    /**
     * 发送通知
     * @return JSON
     */
    public function send(Request $request)
    {
        $user_id = get_user_openid();
        $list = $request->input("list");
        $uri = $request->input("uri");
        $form = Publish::get_form_code($uri);
        $title = $form->form_name;

        $curl = new \anlutro\cURL\cURL();
        $url = env('NOTIFY_URL').'/api/notify/send';
        $rsa_id = RsaCrypt::encode($user_id);
        $response = $curl->post($url,['user'=>$rsa_id, 'code'=>env('AUTH_CODE'), 'accepts'=>$list, 'title'=>$title, 'url'=> "http://" . $_SERVER['HTTP_HOST'] . "/submit/" . $uri]);
        if($response && $response->statusCode == 200){
            $result = json_decode($response->body);
            if($result->code == 0){
                $data = $result->result;
                return Response::json(['status' => 0, 'msg' => $data]);
            } else {
                return Response::json(['status' => 1, 'msg' => "请刷新页面重新尝试"]);
            }
        } else {
            Log::error($response);
            return Response::json(['status' => 1, 'msg' => '网络错误,请稍候尝试']);
        }
    }

    /**
     * 图片上传
     * @param  Request $request
     * @return json
     */
    public function upload_image(Request $request)
    {
        if (!$request->hasFile('file')) {
            return responseToJson(1, '上传文件为空！');
        }
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        if (!in_array(strtolower($ext), array("jpg", "bmp", "png", "gif"))) {
            return responseToJson(5, '只能上传图片！');
        }
        $old = $file->getClientOriginalName();
        if (!$file->isValid()) {
            return responseToJson(2, '(' . $old . ')文件上传出错！');
        }
        $size = $file->getSize();
        $maxSize = 1 * 1024 * 1024;
        if ($size > $maxSize) {
            return responseToJson(3, '单个文件不能超过1M！');
        }
        $filename = md5('wxshare' . time()) . '.' . $ext;

        if (Storage::disk('public')->put($filename, File::get($file))) {
            $file_info = array("original" => $old, "name" => $filename, "size" => $size, "ext" => $ext);
            return responseToJson(0, 'success', $file_info);
            //return responseToJson(0, 'success', "http://" . $_SERVER['HTTP_HOST'] . "/summernote/get/" . $filename);
        } else {
            return responseToJson(4, '(' . $old . ')文件保存出错！');
        }
    }

    /**
     * 将表单导出word文档
     * @return JSON
     */
    public function export_form(Request $request)
    {
        $id = $request->input("id");

        $schema = Publish::get_form($id);

        if(!$schema){
            return '表单不存在，请查询后重新访问！';
        }elseif($schema->status == 0){
            return '表单未发布，请发布后重新下载！';
        }

        $fields = \GuzzleHttp\json_decode($schema->form_schema)->options->fields;


        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection();

        $name = $schema->url_code . ".docx";
        $file_name = $schema->form_name . ".docx";

        $myTextElement = $section->addText($schema->form_name);

        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(true);
        $fontStyle->setName('Cambria');
        $fontStyle->setSize(20);
        $myTextElement->setFontStyle($fontStyle);

        $pStyle = new \PhpOffice\PhpWord\Style\Paragraph();
        $pStyle->setAlignment('center');
        $myTextElement->setParagraphStyle($pStyle);

        $fontTextStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontTextStyle->setName('Cambria');
        $fontTextStyle->setSize(13);

        $fontConStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontConStyle->setName('Cambria');
        $fontConStyle->setSize(11);
        $fontConStyle->setColor('777777');

        $d = 0;
        foreach($fields as $e){
            $d++;
        }

        $k = 1;
        for ($f = 1; $f <= $d ; $f++){
            foreach($fields as $v){
                if($v->order == $f){
                    if(array_key_exists("sourceType", $v)){
                        switch ($v->sourceType) {
                            case "text":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、". $v->label . $widget_content  .":_______________\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                break;
                            case "select":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、（单选）". $v->label . $widget_content ."（   ）\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                foreach($v->optionLabels as $a => $b){
                                    $c = $a + 1;
                                    $textElement = $section->addText("\t" .  "$c" . "）". $b ."\r\n");
                                    $textElement->setFontStyle($fontTextStyle);
                                }
                                break;
                            case "radio":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、（单选）". $v->label . $widget_content ."（   ）\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                foreach($v->optionLabels as $a => $b){
                                    $c = $a + 1;
                                    $textElement = $section->addText("\t" .  "$c" . "）". $b ."\r\n");
                                    $textElement->setFontStyle($fontTextStyle);
                                }
                                break;
                            case "checkbox":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、（多选）". $v->label . $widget_content ."（   ）\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                foreach($v->optionLabels as $a => $b){
                                    $c = $a + 1;
                                    $textElement = $section->addText("\t" .  "$c" . "）". $b ."\r\n");
                                    $textElement->setFontStyle($fontTextStyle);
                                }
                                break;
                            case "textarea":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、". $v->label . $widget_content .":_______________\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                break;
                            case "date":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、". $v->label . $widget_content .":_______________\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                break;
                            case "description":
                                $text = $v->widget_content ."\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontConStyle);
                                $section->addText("\r\n");
                                break;
                            case "number":
                                if(array_key_exists("widget_description", $v) && $v->widget_description != ""){
                                    $widget_content = "（" . $v->widget_description . "）";
                                }else{
                                    $widget_content = "";
                                }
                                $text = $k++ . "、". $v->label . $widget_content  .":_______________\r\n";
                                $textElement = $section->addText($text);
                                $textElement->setFontStyle($fontTextStyle);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("../storage/app/formword/" . $name);

        //3.下载
        $file_dir_path = storage_path('app' . DIRECTORY_SEPARATOR . 'formword' . DIRECTORY_SEPARATOR );
        return response()->download($file_dir_path . $name, $file_name);
    }

    /**
     * 表单附件上传
     * @param  Request $request
     * @return json
     */
    public function upload_submit(Request $request)
    {
        if (!$request->hasFile('file')) {
            return responseToJson(1, '上传文件为空！');
        }
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $old = $file->getClientOriginalName();
        if (!$file->isValid()) {
            return responseToJson(2, '(' . $old . ')文件上传出错！');
        }
        $size = $file->getSize();
        $maxSize = 1 * 1024 * 1024;
        if ($size > $maxSize) {
            return responseToJson(3, '单个文件不能超过1M！');
        }
        $filename = md5('form' . time()) . '.' . $ext;

        if (Storage::disk('public')->put($filename, File::get($file))) {
            $file_info = array("original" => $old, "name" => $filename, "size" => $size);
            return responseToJson(0, 'success', $file_info);
        } else {
            return responseToJson(4, '(' . $old . ')文件保存出错！');
        }
    }
}