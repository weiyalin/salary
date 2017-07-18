@extends('mobile/layout')

@section('title', "{$detail->send_year}年{$detail->send_month}月工资发放通知")

@section('content')
    <div style="text-align: center; margin-top: 1em;">
        <img style="width: 64px; height: 64px;" src="{{ $user->avatar }}" />
        <p>
            {{ $user->name }}
        </p>
    </div>
    <div class="weui-cells__title">{{ $detail->send_year }} 年 {{ $detail->send_month }} 月工资发放详情</div>
    <div class="weui-cells">
        @foreach(range(1, 50) as $i)
            @if (isset($template->{"c{$i}"}) && $template->{"c{$i}"})
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>{{ $template->{"c{$i}"} }}</p>
                </div>
                <div class="weui-cell__ft">{{ $detail->{"c{$i}"} }}</div>
            </div>
            @endif
        @endforeach

        @if ($detail->is_feedback)
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <p>我反馈的内容</p>
            </div>
            <div class="weui-cell__ft">{{ $detail->feedback_content }}</div>
        </div>
        @endif

        @if ($detail->reply_content)
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>回复的内容</p>
                </div>
                <div class="weui-cell__ft">{{ $detail->reply_content }}</div>
            </div>
        @endif

    </div>
    @if ($config->is_enable_feedback && !$detail->is_feedback)
    <div class="button-sp-area">
        <a href="{{ url('/h5/bill/reply', [$detail->id]) }}" class="weui-btn weui-btn_primary">有疑问，我要反馈</a>
    </div>
    @endif
    @if ($config->is_enable_destroy)
        <div class="button-sp-area" style="text-align: center;">
            <a style="color: red;" href="javascript:;" onclick="document.getElementById('dialog').style.display = 'block'">销毁</a>
        </div>
    @endif
    <div class="js_dialog" id="dialog" style="opacity: 1; display: none;">
        <div class="weui-mask"></div>
        <div class="weui-dialog weui-skin_android">
            <div class="weui-dialog__bd">
                确认销毁工资单？
            </div>
            <div class="weui-dialog__ft">
                <a href="javascript:;" onclick="document.getElementById('dialog').style.display = 'none'" class="weui-dialog__btn weui-dialog__btn_default">取消</a>
                <a href="{{ url('/h5/bill/destroy', [$detail->id]) }}" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
            </div>
        </div>
    </div>
@endsection