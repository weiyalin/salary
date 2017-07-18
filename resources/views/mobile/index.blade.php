@extends('mobile/layout')

@section('title', '个人中心')

@section('content')
    <div style="text-align: center; margin-top: 1em;">
        <img style="width: 64px; height: 64px;" src="{{ $user->avatar }}" />
        <p>
            {{ $user->name }}
        </p>
    </div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="/h5/bill">
            <div class="weui-cell__hd">
                <img src="/dist/img/bill.png" style="margin-right: 6px; display: block;">
            </div>
            <div class="weui-cell__bd">
                <p>我的工资单</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="/h5/reset">
            <div class="weui-cell__hd">
                <img src="/dist/img/password.png" style="margin-right: 6px; display: block;">
            </div>
            <div class="weui-cell__bd">
                <p>设置查看密码</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="/h5/profile">
            <div class="weui-cell__hd">
                <img src="/dist/img/profile.png" style="margin-right: 6px; display: block;">
            </div>
            <div class="weui-cell__bd">
                <p>个人详情</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
    </div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="/h5/feedback/list">
            <div class="weui-cell__hd">
                <img src="/dist/img/feedback.png" style="margin-right:5px;display:block">
            </div>
            <div class="weui-cell__bd">
                <p>意见反馈</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
    </div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="/h5/help">
            <div class="weui-cell__hd">
                <img src="/dist/img/help.png" style="margin-right: 6px; display: block;">
            </div>
            <div class="weui-cell__bd">
                <p>在线帮助</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
    </div>

    <div class="weui-tabbar">
        <a href="/h5/logout" class="weui-tabbar__item weui-bar__item_on">
            <span style="display: inline-block;position: relative;">
                <img src="/dist/img/logout.png" class="weui-tabbar__icon">
            </span>
            <p class="weui-tabbar__label">退出</p>
        </a>
    </div>
@endsection