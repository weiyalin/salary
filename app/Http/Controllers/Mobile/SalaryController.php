<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Config;
use App\Models\Feedback;
use App\Models\Salary;
use App\Models\User;
use App\Models\SalaryDetail;
use Stoneworld\Wechat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalaryController extends Controller
{
    /**
     * 个人中心 主页
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $user = session('h5_user');

        return view('mobile/index', ['user' => $user]);
    }

    /**
     * 清理 session
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->flush();


        return redirect('/h5');
    }

    /**
     * 如果有密码，登入钱要输入密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function password(Request $request)
    {
        $password = $request->input('password');
        $user = session('h5_user');

        if ($password)
        {
            if (md5($password) == $user['salary_password'])
            {
                // 密码正确
                $user['salary_password_checked'] = true;
                session('h5_user', $user);

                $path = session('redirect_path');

                if ($path)
                {
                    return redirect($path);
                }
                else
                {
                    return redirect('/h5');
                }
            } else {
                return view('mobile/password', ['message' => '刚刚输入的密码错误']);
            }
        }
        else if (isset($user['salary_password_checked']) && $user['salary_password_checked'])
        {
            return redirect('/h5');
        }

        return view('mobile/password');
    }

    /**
     * 用户个人信息页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile(Request $request)
    {
        $user = session('h5_user');

        return view('mobile/profile', ['user' => $user]);
    }

    /**
     * 工资单列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bill(Request $request, $year=null)
    {
        $year = $year ? $year : date('Y');
        $user = session('h5_user');

        $query = SalaryDetail::year($year)
            ->code($user['code'])
            ->where('is_send', 1)
            ->where('is_destroy', 0)
            ->where('is_delete', 0);

        return view('mobile/bill', [
            'year' => $year,
            'bill' => $query->get()
        ]);
    }

    /**
     * 工资单详情
     * @param Request $request
     * @param $id Salary ID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bill_detail(Request $request, $id)
    {
        $user = session('h5_user');

        $detail = SalaryDetail::where('salary_id', $id)
            ->code($user['code'])
            ->where('is_send', 1)
            ->where('is_destroy', 0)
            ->where('is_delete', 0)
            ->first();

        if (!$detail)
        {
            return redirect('/h5/bill');
        }
        else if (!$detail->is_read)
        {
            $detail->is_read = 1;
            $detail->save();
        }

        $config = Config::first();

        return view('mobile/bill_detail', [
            'user' => $user,
            'detail' => $detail,
            'config' => $config,
            'template' => $detail->template
        ]);
    }

    /**
     * 回复
     * @param Request $request
     * @param $id
     * @return \Illuminate\View\View
     */
    public function bill_reply(Request $request, $id)
    {
        $user = session('h5_user');

        $detail = SalaryDetail::where('id', $id)
            ->code($user['code'])
            ->where('is_send', 1)
            ->where('is_destroy', 0)
            ->where('is_delete', 0)
            ->where('is_feedback', 0) // 未回复才能查看回复页面
            ->first();

        // 记录不存在或已回复
        if (!$detail)
        {
            return redirect(url('/h5/bill'));
        }
        else if (!$detail->feedback_content)
        {
            $content = $request->input('content');

            if ($detail && $content)
            {
                $detail->is_feedback = 1;
                $detail->feedback_content = $content;
                $detail->feedback_time = millisecond();

                $detail->save();

                // 更新工资条统计
                $feedback_count = SalaryDetail::where('salary_id', $detail->salary_id)
                    ->where('is_feedback', 1)
                    ->count();

                Salary::where('id', $detail->salary_id)
                    ->update([
                        'feedback_count' => $feedback_count,
                        'feedback_last_time' => millisecond()
                    ]);

                // 给原始发送人发通知有人回复
                $touser = User::where('id', $detail->create_user_id)->value('code');

                // 调用微信api发送通知
                $text = new Wechat\Messages\Text();
                $text->content = "有员工对{$detail->send_year}年{$detail->send_month}月工资单提出疑问，请登录后台处理";

                $broadCast = new Wechat\Broadcast(get_qy_appid(), get_qy_salary_secret());
                $broadCast->fromAgentId(get_qy_salary_agent());
                $broadCast->send($text);

                $broadCast->to($touser);

                return redirect(url('/h5/bill/detail', [$detail->salary_id]));
            }
        }

        return view('mobile/bill_reply', ['id' => $id]);
    }

    public function bill_destroy(Request $request, $id)
    {
        $user = session('h5_user');
        $detail = SalaryDetail::where('id', $id)
            ->code($user['code'])
            ->where('is_send', 1)
            ->where('is_destroy', 0)
            ->where('is_delete', 0)
            ->first();

        if ($detail)
        {
            $detail->is_destroy = 1;
            $detail->destroy_time = millisecond();
            $detail->save();
        }

        return redirect('/h5/bill');
    }

    /**
     * 重置密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reset_password(Request $request)
    {
        $user = User::where('id', session('h5_user')['id'])->first();

        $new_password = $request->input('new_password');

        if ($new_password)
        {
            // 设置密码
            if ($user->salary_password)
            {
                // 有旧密码
                $password = $request->input('password');

                if (md5($password) != $user->salary_password)
                {
                    return view('mobile/reset', [
                        'user' => $user,
                        'message' => '验证旧密码失败'
                    ]);
                }
            }

            $confirm_password = $request->input('confirm_password');

            if ($new_password == $confirm_password)
            {
                $user->salary_password = md5($new_password);
                $user->save();

                return redirect(url("/h5"));
            }
            else
            {
                return view('mobile/reset', [
                    'user' => $user,
                    'message' => '新密码与确认密码不同'
                ]);
            }
        }

        return view('mobile/reset', ['user' => $user]);
    }

    /**
     * 帮助
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function help(Request $request, $id=null)
    {
        if (!$id)
        {
            return view('mobile/help/list');
        }
        else if (in_array($id, range(1, 3)))
        {
            return view("mobile/help/{$id}");
        }
    }

    /**
     * 反馈
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function feedback(Request $request)
    {
        $user = session('h5_user');
        $content = $request->input('content');

        if ($content)
        {
            $feedback = new Feedback();
            $feedback->content = $content;
            $feedback->user_id = $user['id'];
            $feedback->user_name = $user['name'];
            $feedback->save();

            return redirect(url("/h5/feedback/list"));
        }

        return view('mobile/feedback');
    }

    public function feedback_list(Request $request)
    {
        $user = session('h5_user');

        $query = Feedback::where('user_id', $user['id'])
            ->orderBy('id', 'desc');

        return view('mobile/feedback_list', [
            'feedback' => $query->get()
        ]);
    }

}