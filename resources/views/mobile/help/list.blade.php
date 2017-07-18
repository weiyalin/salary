@extends('mobile/layout')

@section('title', '帮助列表')

@section('content')
    <div class="weui-panel">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="/h5/help/1">
                        <div class="weui-cell__bd">
                            <p>如何邀请员工关注企业号</p>
                        </div>
                        <div class="weui-cell__ft"></div>
                    </a>
                </div>
            </div>
        </div>
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="/h5/help/2">
                        <div class="weui-cell__bd">
                            <p>如何设置工资条查看密码</p>
                        </div>
                        <div class="weui-cell__ft"></div>
                    </a>
                </div>
            </div>
        </div>
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    <a class="weui-cell weui-cell_access" href="/h5/help/3">
                        <div class="weui-cell__bd">
                            <p>对工资条有疑问如何反馈</p>
                        </div>
                        <div class="weui-cell__ft"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection