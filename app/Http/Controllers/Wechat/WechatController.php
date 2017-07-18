<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use Log;
use DB;

class WechatController extends Controller
{
    /**
     * 处理微信的请求消息
     * @return string
     */
    public function serve()
    {
        $wechat = app('wechat');
        $wechat->server->setMessageHandler(function ($message) use ($wechat) {
            //{"ToUserName":"gh_d96276df473d","FromUserName":"oMMATt2Ps82DK_2sGP4QgEJxOUww","CreateTime":"1458799345","MsgType":"text","Content":"看","MsgId":"6265495478605632959"}
            $wxServer = new WxServer($message, $wechat);
            //Message detail: {"ToUserName":"gh_d96276df473d","FromUserName":"oMMATt0KjFrtIcjpfVuRPsVWyqGk","CreateTime":"1458799194","MsgType":"event","Event":"subscribe","EventKey":[]}
            if ($message->MsgType == 'event') {
                if ($message->Event == 'subscribe') {
                    //TODO 关注
                    return $wxServer->subscribe();
                } else if ($message->Event == 'unsubscribe') {
                    //TODO 取消关注
                    return $wxServer->unsubscribe();
                }

                // {"ToUserName":"gh_9f675e9c1f81","FromUserName":"ofeo0szCbJLREJi4KrMP1BeQbtMo","CreateTime":"1459931042","MsgType":"event","Event":"CLICK","EventKey":"event_msg_1"}
                if ($message->Event == 'CLICK') {
                    return $wxServer->click();
                }

            } else if ($message->MsgType == 'text') {
                return $wxServer->keyword();
            }

            return $wxServer->help();

        });

        return $wechat->server->serve();
    }
}

class WxServer
{
    private $message = null;
    private $wechat = null;

    public function __construct($message, $wechat)
    {
        $this->message = $message;
        $this->wechat = $wechat;
    }

    //关注
    public function subscribe()
    {
        $openid = $this->message->FromUserName;
        //获取用户信息
        $userService = $this->wechat->user;
        $userInfo = $userService->get($openid);
        $query = DB::table('wx_user')
            ->where('openid', $openid)
            ->first();

        if ($query) {
            DB::table('wx_user')->where('openid', $openid)->update([
                'nickname' => wx_nickname_filter(strval($userInfo->nickname)),
                'avatar' => strval($userInfo->headimgurl),
                'sex' => intval($userInfo->sex),
                'province' => strval($userInfo->province),
                'city' => strval($userInfo->city),
                'country' => strval($userInfo->country),
                'is_subscribe' => intval($userInfo->subscribe),
                'subscribe_time' => strval(intval($userInfo->subscribe_time)),
                'create_time' => time(),
                'unionid'=> strval($userInfo->unionid)
            ]);
        } else {
            DB::table('wx_user')->insert([
                'openid' => $openid,
                'nickname' => wx_nickname_filter(strval($userInfo->nickname)),
                'avatar' => strval($userInfo->headimgurl),
                'sex' => intval($userInfo->sex),
                'province' => strval($userInfo->province),
                'city' => strval($userInfo->city),
                'country' => strval($userInfo->country),
                'is_subscribe' => intval($userInfo->subscribe),
                'subscribe_time' => strval(intval($userInfo->subscribe_time)),
                'create_time' => time(),
                'unionid'=> strval($userInfo->unionid)
            ]);
        }
        //TODO 返回关注公众号时配置的信息
        //类型，0：被添加自动回复(关注时回复)，1：消息自动回复（关键字匹配不到回复），2：关键词自动回复
        $autoReply = DB::table('wx_autoreply')
            ->where('category', 0)
            ->where('status', 0)
            ->first();
        if ($autoReply) {
            $subContent = $autoReply->content;
        } else {
            $subContent = '欢迎关注~~~';
        }
        return $subContent;
    }

    //取消关注
    public function unsubscribe()
    {
        $openid = $this->message->FromUserName;

        $query = DB::table('wx_user')
            ->where('openid', $openid)
            ->update([
                'is_subscribe' => 0,
                'subscribe_time' => 0
            ]);

        return $query !== false ? true : false;
    }

    /**
     * 单击事件
     * @return string
     */
    public function click()
    {
        //TODO 暂时不处理点击事件
        return $this->help();
//        return 'click';
    }

    //关键字匹配
    public function keyword()
    {
        //TODO
        $content = $this->message->Content;
        $keywordRlt = DB::table('wx_keyword')
            ->where('name', $content)
            ->first();
        if ($keywordRlt) {
            //查找到
            $autoReply = DB::table('wx_autoreply')
                ->where('id', $keywordRlt->reply_id)
                ->first();
            if ($autoReply) {
                return $autoReply->content;
            }
        }
        //没有查找到,查默认配置
        $autoReply = DB::table('wx_autoreply')
            ->where('category', 1)
            ->where('status', 0)
            ->first();
        if ($autoReply) {
            return $autoReply->content;
        }

        return '无法识别此内容~~';
    }

    public function help()
    {
        //TODO
        return 'help';
    }
}
