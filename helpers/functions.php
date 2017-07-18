<?php

//var_dump(debug_backtrace());

function wx_nickname_filter($name)
{
    $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
    $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S', '?', $name);
    $name = preg_replace('/xE0[x80-x9F][x80-xBF]' . '|xED[xA0-xBF][x80-xBF]/S', '?', $name);
    $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
    return $name;
}

function millisecond()
{
    return ceil(microtime(true) * 1000);
}

/**
 * 页面json 输出
 * @param int $code
 * @param $msg
 * @param $paras
 * @return \Illuminate\Http\JsonResponse
 */
function responseToJson($code = 0, $msg = '', $paras = null)
{
    $res["code"] = $code;
    $res["msg"] = $msg;
    if (!empty($paras)) {
        $res["result"] = $paras;
    }
    return response()->json($res);
}

function get_user_id()
{
    if (empty(session("user"))) {
        //default_user_info();
        return 0;
    }
    return session('user')->id;
}

function get_user_openid()
{
    if (empty(session("wechat.oauth_user"))) {
        //default_user_info();
        return "";
    }

    if (array_key_exists("openid", session('wechat.oauth_user'))) {
        return session('wechat.oauth_user')->openid;
    } else {
        return "";
    }
}

function get_user_info()
{
    if (empty(session("user"))) {
        //default_user_info();
    }
    return session('user');
}

function default_user_info()
{
    $std = new \stdClass();
    $std->id = 1;
    $std->name = "admin";
    $std->phone = "18686868686";
    $std->role_id = 1;
    session(['user' => $std]);
}

function responseToPage($results)
{
    return response()->json($results);
}

function create_uuid($prefix = "")
{    //可以指定前缀
    $str = md5(uniqid(mt_rand(), true));
    $uuid = substr($str, 0, 8) . '-';
    $uuid .= substr($str, 8, 4) . '-';
    $uuid .= substr($str, 12, 4) . '-';
    $uuid .= substr($str, 16, 4) . '-';
    $uuid .= substr($str, 20, 12);
    return $prefix . $uuid;
}

/**
 * 获取用户密码加密字符串
 * @param $password
 * @param $salt
 * @return string
 */
function get_md5_password($password, $salt)
{
    return md5(md5($password) . $salt);
}

function get_salt()
{
    $uuid = create_uuid();
    $salt = substr($uuid, strlen($uuid) - 4, 4);
    return $salt;
}

/**
 * 获取随机串
 * @param int $len 需要几位随机数
 * @param bool $only_digit 是否只要数字
 * @return string
 */
function get_rand_str($len = 4, $only_digit = false)
{
    $chars = '0123456789';
    if (!$only_digit) {
        $chars .= 'abcdefghijklmnopqrstwyz';
    }
    mt_srand((double)microtime() * 1000000 * getmypid());
    $code = "";
    while (strlen($code) < $len)
        $code .= substr($chars, (mt_rand() % strlen($chars)), 1);
    return $code;
}


function get_pinyin_simple($str)
{
    return \Overtrue\LaravelPinyin\Facades\Pinyin::abbr($str);
}

function get_pinyin_all($str)
{
    $arr = \Overtrue\LaravelPinyin\Facades\Pinyin::convert($str);
    return implode("", $arr);
}

// 数字转中文
function number2Chinese($num, $m = 1)
{
    switch ($m) {
        case 0:
            $CNum = array(
                array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'),
                array('', '拾', '佰', '仟'),
                array('', '萬', '億', '萬億')
            );
            break;
        default:
            $CNum = array(
                array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九'),
                array('', '十', '百', '千'),
                array('', '万', '亿', '万亿')
            );
            break;
    }

    if (!is_numeric($num)) {
        return false;
    }

    $flt = '';
    if (is_integer($num)) {
        $num = strval($num);
    } else if (is_numeric($num)) {
        $num = strval($num);
        $rs = explode('.', $num, 2);
        $num = $rs[0];
        $flt = $rs[1];
    }

    $len = strlen($num);
    $num = strrev($num);
    $chinese = '';

    for ($i = 0, $k = 0; $i < $len; $i += 4, $k++) {
        $tmp_str = '';
        $str = strrev(substr($num, $i, 4));
        $str = str_pad($str, 4, '0', STR_PAD_LEFT);
        for ($j = 0; $j < 4; $j++) {
            if ($str{$j} !== '0') {
                $tmp_str .= $CNum[0][$str{$j}] . $CNum[1][4 - 1 - $j];
            }
        }
        $tmp_str .= $CNum[2][$k];
        $chinese = $tmp_str . $chinese;
        unset($str);
    }
    if ($flt !== '') {
        $str = '';
        for ($i = 0; $i < strlen($flt); $i++) {
            $str .= $CNum[0][$flt{$i}];
        }
        $chinese .= "点{$str}";
    }
    return $chinese;
}

/**
 * 生成pdf
 * @param $url
 * @param array $params
 * @return null|string null:生成失败
 */
function generate_pdf($url, $params = [])
{
    $filename = millisecond() . '.pdf';
    $path = storage_path("app/pdf/{$filename}");

    $default_params = [
        '-q' => '',
        '--page-size' => 'A3',
        '--print-media-type' => '',
        '--dpi' => '300',
        '--orientation' => 'Landscape',
        '--zoom' => '1',
        '--margin-top' => '0px',
        '--margin-bottom' => '0px',
        '--margin-left' => '0px',
        '--margin-right' => '0px',
    ];

    if (count($params)) {
        $params = array_merge($default_params, $params);
    } else {
        $params = $default_params;
    }

    $result = [];
    foreach ($params as $key => $value) {
        $result[] = "{$key} {$value}";
    }

    $wkhtmltopdf = sprintf(env('WKHTMLTOPDF_PATH') . " %s %s " . $path, implode(' ', $result), $url);

    if (config('app.debug')) {
        //
        file_put_contents(storage_path("app/pdf/debug.txt"), date('Y-m-d H:i:s') . '    ' . $wkhtmltopdf . PHP_EOL, FILE_APPEND);
    }

    $rlt = shell_exec($wkhtmltopdf);

    if (file_exists($path)) {
        return $filename;
    }

    return null;
}

/**
 * 依据纸张类型获取纸张大小
 * @param $paper_type A3,A4等
 * @return mixed
 */
function get_paper_size($paper_type)
{
    //TODO 完善
    switch (strtoupper($paper_type)) {
        case 'A3':
        default:
            $size['width'] = 1190;
            $size['height'] = 842;
            $size['dpi'] = 72;
            break;
    }
    return $size;
}

function filter_filename($filename)
{
    return str_replace(
        array('\\', '/', ':', '*', '"', '?', '<', '>', '|'),
        array('', '', '', '', '', '', '', '', ''),
        $filename
    );
}

function get_qy_appid()
{
    return env('CorpID');
}

function get_qy_salary_agent()
{
    return env('AgentId');
}

function get_qy_salary_secret()
{
    return env('Secret');
}

//生成工资表密码
function generate_salary_password($password)
{
    return md5($password);
}