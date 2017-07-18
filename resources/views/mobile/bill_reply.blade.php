@extends('mobile/layout')

@section('title', '反馈')

@section('content')
    <form method="post" action="{{ url('/h5/bill/reply', [$id]) }}">
        {{ csrf_field() }}
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <textarea class="weui-textarea" placeholder="请输入你的问题" rows="3" name="content" required></textarea>
                </div>
            </div>
        </div>
        @if (isset($message))
            <div class="weui-cells__tips">{{ $message }}</div>
        @endif
        <div class="button-sp-area">
            <button type="submit" class="weui-btn weui-btn_primary">确定</button>
        </div>
    </form>
@endsection