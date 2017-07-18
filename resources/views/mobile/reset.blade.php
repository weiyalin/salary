@extends('mobile/layout')

@section('title', '设置查看密码')

@section('content')
    <form method="post" action="/h5/reset">
        <div class="weui-cells">
            @if ($user->salary_password)
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" name="password" placeholder="请输入当前密码" required>
                </div>
            </div>
            @endif
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" name="new_password" placeholder="请输入新密码" required>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <input class="weui-input" type="password" name="confirm_password" placeholder="请确认新密码" required>
                </div>
            </div>
        </div>
        @if (isset($message))
            <div class="weui-cells__tips">{{ $message }}</div>
        @endif
        {{ csrf_field() }}
        <div class="button-sp-area">
            <button type="submit" class="weui-btn weui-btn_primary">确定</button>
        </div>
    </form>
@endsection