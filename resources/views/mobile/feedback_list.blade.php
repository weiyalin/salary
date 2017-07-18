@extends('mobile/layout')

@section('title', '我的反馈')

@section('content')

<div class="weui-panel">
    <div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_small-appmsg">
            <div class="weui-cells">
                @if ($feedback->count() > 0)
                    @foreach($feedback as $item)
                        <a class="weui-cell weui-cell_access">
                            <div class="weui-cell__bd">
                                <p>我的反馈：{{ $item->content }}</p>

                                @if ($item->reply_content)
                                    <p>回复：{{ $item->reply_content }}</p>
                                @endif

                                <span class="small-text">
                                    {{ $item->created_at->year }}-{{ $item->created_at->month }}-{{ $item->created_at->day }}
                                    {{ $item->created_at->hour }}:{{ $item->created_at->minute }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                @else
                    <a class="weui-cell weui-cell_access">
                        <div class="weui-cell__bd">
                            <p>你还没有反馈过</p>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="weui-tabbar">
    <a href="/h5/feedback" class="weui-tabbar__item weui-bar__item_on">
        <span style="display: inline-block;position: relative;">
            <img src="/dist/img/feedback.png" class="weui-tabbar__icon">
        </span>
        <p class="weui-tabbar__label">我要反馈</p>
    </a>
</div>
@endsection