<?php

namespace App\Models;

use DB;
use Stoneworld\Wechat;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    //工资表

    public $table = 'salary';
    public $timestamps = false;

    static function search_salary($params)
    {
        $pageSize = $params['page_size'];
        $salaryName = $params['salary_name'];
        if (!empty($params['send_month'])) {
            list($sendYear, $sendMonth) = explode('-', $params['send_month']);
        } else {
            $sendYear = 0;
            $sendMonth = 0;
        }


        $query = DB::table('salary')
            ->where('is_delete', '0');

        if ($sendYear) {
            $query->where('send_year', $sendYear);
        }
        if ($sendMonth) {
            $query->where('send_month', $sendMonth);
        }

        if ($salaryName) {
            $query->where('name', 'like', '%' . $salaryName . '%');
        }

        //TODO 能够查看的工资表权限
        $self = get_user_info();
        if ($self) {
            //查看自己创建的及其管理部门下人员创建的工资条
            if ($self->is_super_manager) {
                //超管,不限定
            } else if ($self->is_grade_manager) {
                //分组管理员
                $manageDeparts = explode(',', trim($self->manage_department, ','));
                $orgIds = Org::get_org_ids($manageDeparts);
                $userIds = UserOrg::whereIn('org_id', $orgIds)->pluck('user_id')->toArray();//TODO 可以优化
                $userIds[] = $self->id;//包含自己
                $query->whereIn('create_user_id', $userIds);
            } else {
                //普通用户
                $query->where('id', 0);//没有权限,不能查看
            }
        } else {
            $query->where('id', 0);//没有权限,不能查看
        }

        $query->orderBy('id', 'DESC');

        return $query->paginate($pageSize);
    }

    static function search_salary_detail($params)
    {
        $pageSize = $params['page_size'];
        $salaryId = $params['salary_id'];
        $keyword = trim($params['keyword']);
        $feedback = $params['feedback'];
        $send = $params['send'];
        $departId = $params['depart_id'];

        $self = get_user_info();

        $salary = DB::table('salary')
            ->where('id', $salaryId)
            ->where('is_delete', 0)
            ->first();

        //判断是否有访问此salary的权限
        if (!self::has_permission($self, $salary)) {
            return ['tpl' => [], 'data' => DB::table('salary_detail')->where('id', 0)->paginate($pageSize)];
        }

        //查看详情
        $query = DB::table('salary_detail')
            ->select('id', 'salary_id', 'send_title', 'is_feedback', 'feedback_content', 'is_reply', 'reply_content', 'is_send', 'is_read', 'code')
            ->where('is_delete', '0')
            ->where('salary_id', $salaryId);//指定获取某些字段

        $nameColKey = null;
        $tplCols = [];
        foreach (TemplateSnapshot::fetch_cols($salary->tpl_snapshot_id) as $key => $val) {
            if ($val == '姓名') {
                $nameColKey = $key;
            }
            if ($val != '工号') {
                $tplCols[$key] = $val;
                $query->addSelect($key);
            }
        }

        if ($send != -1) {
            $query->where('is_send', $send);
        }

        if ($feedback != -1) {
            $query->where('is_feedback', $feedback);
        }

        if ($keyword) {
            //姓名、工号
            $query->where(function ($query) use ($keyword, $nameColKey) {
                $query->where('code', 'like', '%' . $keyword . '%');
                if ($nameColKey) {
                    $query->orWhere($nameColKey, 'like', '%' . $keyword . '%');
                }
            });
        }

        if ($departId) {
            //部门过滤
            $depart = Org::where('id', $departId)->first();
            if ($depart) {
                $departIds = self::get_manage_orgs($self, $depart);
                $userIds = UserOrg::whereIn('org_id', $departIds)->pluck('user_id');
                $codeList = User::whereIn('id', $userIds)->pluck('code');

                $query->whereIn('code', $codeList);
            } else {
                $query->where('id', 0);//指定的组织机构不存在
            }
        }

        return ['tpl' => $tplCols, 'data' => $query->paginate($pageSize)];
    }

    //获取管理的组织机构ID
    static function get_manage_orgs($user, $org)
    {
        $departIds = Org::where('path', 'like', $org->path . $org->id . '-%')->pluck('id')->toArray();
        $departIds[] = $org->id;
        if ($user->is_super_manager) {
            //
        } else if ($user->is_grade_manager) {
            //获取权限范围内的组织机构
            $userManageOrgs = explode(',', trim($user->manage_department, ','));
            $departIds = array_intersect($departIds, $userManageOrgs);
        } else {
            //普通用户
            //不允许普通用户搜索
            return [];
        }
        return $departIds;
    }

    static function send_salary($salary_id, $ids)
    {
        $query = DB::table('salary_detail');
        $query->where('salary_id', $salary_id);
        if ($ids == '-1') {

        } else if (is_array($ids)) {
            $query->whereIn('id', $ids);
        } else {
            return false;
        }

        $query->update([
            'is_send' => 1,
            'send_time' => millisecond()
        ]);

        //调用微信api发送通知
        $userCodeList = $query->pluck('code')->toArray();
        $notifyUrl = (request()->server('REQUEST_SCHEME') ? request()->server('REQUEST_SCHEME') : 'http') . '://' . request()->server('HTTP_HOST') . '/h5/bill/detail/' . $salary_id;
        $salary = DB::table('salary')->where('id', $salary_id)->first();
        QyWechat::send_message($userCodeList, $notifyUrl, $salary->send_year, $salary->send_month);

        $totalPerson = $salary->total_person;//总人数

        $sendCount = DB::table('salary_detail')->where('salary_id', $salary_id)->where('is_send', 1)->count();//已发人数

        //更新salary统计字段
        DB::table('salary')->where('id', $salary_id)->update([
            'send_count' => $sendCount,
            'send_last_time' => millisecond(),
            'unsend_count' => $totalPerson - $sendCount
        ]);
    }

    static function delete_salary($salary_id)
    {
        $query = DB::table('salary');
        $query->where('id', $salary_id);
        $query->update([
            'is_delete' => 1,
            'delete_at' => millisecond()
        ]);
        self::delete_detail_salary($salary_id, -1);
    }

    static function delete_detail_salary($salary_id, $ids)
    {
        $query = DB::table('salary_detail');
        $query->where('salary_id', $salary_id);
        if ($ids == -1) {

        } else if (is_array($ids)) {
            $query->whereIn('id', $ids);
        } else {
            return false;
        }

        $query->update([
            'is_delete' => 1,
            'delete_at' => millisecond()
        ]);

        //重新统计salary表中发送总数、已发送数、反馈数
        $totalPerson = DB::table('salary_detail')->where('salary_id', $salary_id)->where('is_delete', 0)->count();//总人数

        $sendCount = DB::table('salary_detail')->where('salary_id', $salary_id)->where('is_send', 1)->where('is_delete', 0)->count();//已发人数

        $feedbackCount = DB::table('salary_detail')->where('salary_id', $salary_id)->where('is_feedback', 1)->where('is_delete', 0)->count();//反馈人数

        //更新salary统计字段
        DB::table('salary')->where('id', $salary_id)->update([
            'total_person' => $totalPerson,
            'send_count' => $sendCount,
            'unsend_count' => $totalPerson - $sendCount,
            'feedback_count' => $feedbackCount
        ]);
    }


    //回复员工反馈
    static function reply_feedback($reply)
    {
        $query = DB::table('salary_detail')
            ->where('id', $reply['salary_detail_id'])
            ->where('salary_id', $reply['salary_id']);

        $touser = [$query->value('code')];

        $query->update([
            'is_reply' => 1,
            'reply_content' => $reply['content'],
            'reply_user_id' => $reply['user_id'],
            'reply_user_name' => $reply['user_name'],
            'reply_time' => millisecond()
        ]);

        // 调用微信api发送通知
        $notifyUrl = (request()->server('REQUEST_SCHEME') ? request()->server('REQUEST_SCHEME') : 'http') . '://' . request()->server('HTTP_HOST') . '/h5/bill/detail/' . $reply['salary_id'];
        $salary = DB::table('salary')->where('id', $reply['salary_id'])->first();

        $count = count($touser);

        if ($count > 1000) {
            $list = [];
            for ($i = 0; $i < $count; $i += 1000) {
                $list[] = array_slice($touser, $i, 1000);
            }

        } else {
            $list = [$touser];
        }

        $config = DB::table('config')->first();
        $news = new Wechat\Messages\News();
        $item = new Wechat\Messages\NewsItem();
        $item->title = "{$salary->send_year}年{$salary->send_month}月工资单反馈回复";
        $item->description = "您的疑问已经有了解答，请查看";
        $item->pic_url = env('APP_URL') . '/dist/img/reply_notify.jpg';//'http://qy.bhuitong.com/images/tongzhi.jpg';
        $item->url = $notifyUrl;

        $news->item($item);

        $broadCast = new Wechat\Broadcast(get_qy_appid(), get_qy_salary_secret());
        $broadCast->fromAgentId(get_qy_salary_agent());
        $broadCast->send($news);

        foreach ($list as $val) {
            $str = implode("|", $val);
            $broadCast->to($str);
        }
    }

    //设置工资条密码
    static function set_password($salary_id, $password)
    {
        $password = generate_salary_password($password);

        $query = DB::table('salary')
            ->where('id', $salary_id)
            ->update([
                'password' => $password,
            ]);
    }

    //验证密码是否正确
    static function auth_password($salary_id, $password)
    {
        $password = generate_salary_password($password);

        $query = DB::table('salary')
            ->where('id', $salary_id)
            ->where('password', $password)
            ->count();
        return $query > 0 ? true : false;
    }

    //是否验证过密码
    static function is_valid_password($salary_id)
    {
        if (session('salary_password_' . $salary_id)) {
            return true;
        }
        return false;
    }

    //缓存salary密码
    static function cache_salary_password($salary_id)
    {
        session(['salary_password_' . $salary_id => true]);
    }

    static function get_salary($salary_id)
    {
        return DB::table('salary')->where('id', $salary_id)->where('is_delete', 0)->first();
    }

    /**
     * 用户是否有权限访问工资条
     * @param $user
     * @param $salary
     * @return bool
     */
    static function has_permission($user, $salary)
    {
        if (!$salary) {
            return false;
        }
        if ($user->is_super_manager) {
            return true;
        } else if ($user->is_grade_manager) {
            //获取权限范围内的组织机构
            if ($salary->create_user_id == $user->id) {
                return true;
            }

            $userManageOrgs = explode(',', trim($user->manage_department, ','));

            $query = DB::table('user_org')->whereIn('org_id', $userManageOrgs)->where('user_id', $salary->create_user_id)->first();
            if ($query) {
                return true;
            }
        }

        return false;
    }
}
