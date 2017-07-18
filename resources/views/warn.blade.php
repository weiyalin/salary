<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="{{csrf_token()}}" id="csrf_token" name="csrf_token"/>
    <title>{{ config('app.company_name') }}-{{ config('app.app_name') }}</title>

    <style type="text/css">
        body {
            font-family: "Microsoft Yahei";
            background-color: #edf0f8;
            padding:0;
            margin: 0px;
        }

        .main{
            padding: 30px;
            text-align: -webkit-center;
            -webkit-overflow-scrolling:touch;
        }

        .main_box {
            width: 100%;
            max-width: 800px;
            text-align: -webkit-left;
            background-color: #fff;
            position: relative;
        }

        #form_box{
            height:500px;
        }

        @media screen and (max-width: 600px) {
            .main {
                padding:0px;
            }
        }

        .icon{
            font-size: 132px;
            text-align: center;
            padding-top: 120px;
        }
    </style>
</head>
<body>
<div class="main">
    <div class="main_box">
        <div id="form_box">
            @if ($code == 0)
                <div class="icon">
                    <img src="{{ url('img/admin/success.png') }}">
                </div>
            @elseif ($code == 1)
                <div class="icon">
                    <img src="{{ url('img/admin/warning.png') }}">
                </div>
            @elseif ($code == 2)
                <div class="icon">
                    <img src="{{ url('img/admin/warning.png') }}">
                </div>
            @endif
            <div style="text-align: center;font-size: 18px;">{{$msg}}</div>
            @if ($code == 2)
                <div style="text-align: center;font-size: 18px;margin-top: 15px;">点击跳转 <a href="http://www.baidu.com">www.baidu.com</a> 进行登录</div>
            @endif
        </div>
    </div>
    <div class="footer" style="bottom:0px; text-align: center;color: #777;margin:20px;font-size:13px;">
        由伽马科技提供技术支持
    </div>
</div>
</body>
</html>
