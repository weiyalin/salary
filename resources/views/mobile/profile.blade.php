@extends('mobile/layout')

@section('title', '个人信息')

@section('content')
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <img src="/dist/img/profile.png" style="margin-right: 6px; display: block;">
            </div>
            <div class="weui-cell__bd">
                <p>姓名</p>
            </div>
            <div class="weui-cell__ft">{{ $user['name'] }}</div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <img src="/dist/img/code.png" style="margin-right: 6px; display: block;">
            </div>
            <div class="weui-cell__bd">
                <p style="width: 64px;">工号</p>
            </div>
            <div class="weui-cell__ft" style="word-break: break-all;">{{ $user['code'] }}</div>
        </div>
    </div>
@endsection