<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>{{ env('COMPANY_NAME') }}</title>
    <link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css">
    <style>
        body {
            background-color: #f8f8f8;
        }

        .button-sp-area {
            margin: 0 auto;
            padding: 16px 16px;
        }

        .vux-header {
            position: relative;
            padding: 3px 0;
            box-sizing: border-box;
            @if (strpos($_SERVER['HTTP_USER_AGENT'], 'wxwork'))
            background-color: #5077aa;
            @else
            background-color: #393a3f;
            @endif
        }

        .vux-header .vux-header-left {
            left: 18px;
        }

        .vux-header .vux-header-left, .vux-header .vux-header-right {
            position: absolute;
            top: 14px;
            display: block;
            font-size: 14px;
            line-height: 21px;
            color: #ccc;
        }

        .vux-header .vux-header-back {
            color: white;
        }

        .vux-header .vux-header-title, .vux-header h1 {
            margin: 0 88px;
            line-height: 40px;
            text-align: center;
            height: 40px;
            font-size: 18px;
            font-weight: 400;
            width: auto;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #fff;
        }

        .vux-header .vux-header-right {
            right: 16px;
        }

        .vux-header .vux-header-left, .vux-header .vux-header-right {
            position: absolute;
            top: 14px;
            display: block;
            font-size: 14px;
            line-height: 21px;
            color: #ccc;
        }

        .weui-cell__hd img {
            width: 20px;
        }

        .weui-tabbar__item img {
            width: 22px;
            height: 22px;
        }

        .weui-tabbar__item p {
            margin: 0;
        }

        .weui-tabbar__label {
            margin-bottom: 4px;
            font-size: 14px;
        }

        .weui-tabbar__label {
            color: #5077aa!important;
        }

        .weui-cell__bd {
            font-size: 16px;
        }

        .weui-cells {
            margin-top: 1em;
        }

        .small-text {
            font-size: 12px;
            color: #a0a0b3;
        }
    </style>
</head>
<body>
    @section('header')
    <div class="vux-header" style="width: 100%;">
        <div class="vux-header-left">
            <a class="vux-header-back" href="javascript: history.back()" id="back">
                <img src="/dist/img/back.png" style="width: 20px;">
            </a>
        </div>
        <h1 class="vux-header-title"><span>@yield('title')</span></h1>
        <div class="vux-header-right"><a class="vux-header-more"></a></div>
    </div>
    @show
    @yield('content')

    <script type="text/javascript">
        if (history.length <= 1) {
            document.getElementById("back").style.display = "none"
            console.log("hide")
        }
    </script>
</body>
</html>
