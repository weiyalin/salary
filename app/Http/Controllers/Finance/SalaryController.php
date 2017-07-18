<?php

namespace App\Http\Controllers\Finance;

use App\Models\Salary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalaryController extends Controller
{
    function get_list(Request $request)
    {
        $pageSize = $request->page_size;
        $salaryName = trim($request->salary_name);
        $sendMonth = trim($request->send_month);

        return responseToJson(0, 'success', Salary::search_salary([
            'page_size' => $pageSize ? $pageSize : 50,
            'salary_name' => $salaryName,
            'send_month' => $sendMonth
        ]));
    }

    function get_detail_list(Request $request)
    {
        $pageSize = $request->page_size;
        $salaryId = intval($request->salary_id);
        $keyword = trim($request->keyword);
        $feedback = isset($request->feedback) ? intval($request->feedback) : -1;
        $send = isset($request->send) ? intval($request->send) : -1;
        $departId = intval($request->department_id);

        return responseToJson(0, 'success', Salary::search_salary_detail([
            'page_size' => $pageSize ? $pageSize : 50,
            'salary_id' => $salaryId,
            'keyword' => $keyword,
            'send' => $send,
            'feedback' => $feedback,
            'depart_id' => $departId
        ]));
    }

    function send_salary(Request $request)
    {
        $salaryId = intval($request->salary_id);
        $ids = trim($request->ids);
        if ($ids == '-1') {
            //发全部
        } else {
            $ids = explode(',', $ids);
        }
        Salary::send_salary($salaryId, $ids);
        return responseToJson(0, 'success');
    }

    function delete_salary(Request $request)
    {
        $salaryId = intval($request->id);
        Salary::delete_salary($salaryId);
        return responseToJson(0, 'success');
    }

    function delete_detail_salary(Request $request)
    {
        $salaryId = intval($request->salary_id);
        $ids = trim($request->ids);
        if ($ids == '-1') {
            //发全部
            $ids = intval($ids);
        } else {
            $ids = explode(',', $ids);
        }
        Salary::delete_detail_salary($salaryId, $ids);
        return responseToJson(0, 'success');
    }

    //回复员工反馈
    function reply_feedback(Request $request)
    {
        $salaryId = intval($request->salary_id);
        $replyContent = trim($request->reply_content);
        $id = intval($request->id);

        $user = get_user_info();

        $reply['content'] = $replyContent;
        $reply['salary_id'] = $salaryId;

        $reply['salary_detail_id'] = $id;
        $reply['user_id'] = $user->id;
        $reply['user_name'] = $user->name;

        Salary::reply_feedback($reply);
        return responseToJson(0, 'success');
    }

    function set_password(Request $request)
    {
        $salaryId = intval($request->id);
        $password = trim($request->password);
        if (empty($password)) {
            return responseToJson(1, '请输入密码');
        }
        Salary::set_password($salaryId, $password);
        return responseToJson(0, 'success');
    }

    function auth_password(Request $request)
    {
        $salaryId = intval($request->id);
        $password = trim($request->password);
        if (empty($password)) {
            return responseToJson(1, '请输入密码');
        }
        $pass = Salary::auth_password($salaryId, $password);
        if ($pass) {
            Salary::cache_salary_password($salaryId);
            return responseToJson(0, 'success');
        } else {
            return responseToJson(2, '密码错误,请重新输入');
        }
    }

    function check_password(Request $request)
    {
        $salaryId = intval($request->id);
        $pass = Salary::is_valid_password($salaryId);
        return responseToJson(0, 'success', $pass);
    }

    function get_salary(Request $request)
    {
        $salaryId = intval($request->id);
        $salary = Salary::get_salary($salaryId);

        if ($salary) {
            return responseToJson(0, 'success', $salary);
        } else {
            return responseToJson(1, '工资表不存在或已被删除');
        }
    }


}
