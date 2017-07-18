@extends('mobile/layout')

@section('title', '历史查询')

@section('content')
    @section('header')
    <div class="vux-header" style="width: 100%;">
        <div class="vux-header-left">
            <a class="vux-header-back" href="{{ url('h5/bill', [$year - 1]) }}">
                上一年
            </a>
        </div>
        <h1 class="vux-header-title"><span>{{ $year }} 年</span></h1>
        <div class="vux-header-right">
            @if ($year < date('Y'))
                <a class="vux-header-back" href="{{ url('h5/bill', [$year + 1]) }}">
                    下一年
                </a>
            @else
                <a class="vux-header-back" style="color: #b1b0b0;">
                    下一年
                </a>
            @endif
        </div>
    </div>
    @endsection

    <div class="weui-panel">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    @if ($bill->count() > 0)
                        @foreach($bill as $item)
                            <a class="weui-cell weui-cell_access" href="{{ url('/h5/bill/detail', [$item->salary_id]) }}">
                                <div class="weui-cell__bd">
                                    <p>{{ $item->send_title }}</p>
                                </div>
                                <div class="weui-cell__ft"></div>
                            </a>
                        @endforeach
                    @else
                        <a class="weui-cell weui-cell_access">
                            <div class="weui-cell__bd">
                                <p>当前年份无工资发放记录</p>
                            </div>
                            <div class="weui-cell__ft"></div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="weui-tabbar">
        <a href="/h5" class="weui-tabbar__item weui-bar__item_on">
            <span style="display: inline-block;position: relative;">
                <img src="/dist/img/profile.png" class="weui-tabbar__icon">
            </span>
            <p class="weui-tabbar__label">个人中心</p>
        </a>
    </div>
@endsection