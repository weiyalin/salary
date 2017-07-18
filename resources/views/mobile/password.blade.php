@extends('mobile/layout')

@section('title', '输入密码')

@section('content')
    <form method="post" action="/h5/password">
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" name="password" placeholder="请输入查看密码">
                </div>
            </div>
        </div>
        @if (isset($message))
        <div class="weui-cells__tips">{{ $message }}</div>
        @endif
        {{ csrf_field() }}
        <div class="button-sp-area">
            <button type="submit" class="weui-btn weui-btn_primary">登录</button>
        </div>
    </form>
@endsection