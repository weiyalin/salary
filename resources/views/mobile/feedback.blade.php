@extends('mobile/layout')

@section('title', '意见反馈')

@section('content')
    <form method="post" action="/h5/feedback">
        {{ csrf_field() }}
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <textarea class="weui-textarea" placeholder="您的建议是我们前进的动力" rows="3" name="content" required></textarea>
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