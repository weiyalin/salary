<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf" content="{{csrf_token()}}">
    <title>登录页面-{{ config('app.company_name') }}-{{ config('app.app_name') }}</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            transition: border .5s;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .wrap {
            width: 800px;
            margin: 0 auto;
        }

        .header {
            color: #fff;
            font-size: 30px;
            padding: 100px 0 50px;
            text-align: center;
        }

        .point {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #fff;
            border-radius: 10px;
            line-height: 40px;
            vertical-align: middle;
        }

        .login-box-bg {
            position: absolute;
            background-color: #fbfdff;
            width: 100%;
            height: 100%;
            top: 0;
            right: 0;
            z-index: -1;
            opacity: 0.7;
        }

        .login-box {
            position: relative;
            width: 300px;
            border: 1px solid #eaf1f1;
            border-radius: 3px;
            padding: 15px 25px;
            margin: 0 auto;
            z-index: 1;
        }

        .login-form div {
            z-index: 100;
        }

        .login-input {
            margin-top: 2em;
            border: 1px solid #cdcdcd;
            border-radius: 3px;
            padding: 8px 10px;
            background-color: #F5FFFF;
        }

        .login-input input {
            width: 220px;
            height: 25px;
            padding: 0 15px;
            line-height: 20px;
            border: 0;
            outline: none;
            display: block;
            font-size: 14px;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 15px #fbfdff inset;
        }

        input {
            background: rgba(0, 0, 0, 0);
        }

        #submit {
            margin: 25px 0;
            background-color: #3099e9;
            color: #fff;
            width: 300px;
            border: 0;
            border-radius: 3px;
            font-size: 18px;
            padding: 8px 0;
            cursor: pointer;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            text-align: center;
            color: #fff;
            width: 100%;
            font-weight: 300;
        }

        #tip {
            height: 10px;
            line-height: 15px;
            text-align: center;
            color: #F76260;
            font-size: 14px;
        }

        body, div, span, button, code, input, select, textarea {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans", "wenquanyi micro hei", "Hiragino Sans GB", "Hiragino Sans GB W3", Arial, sans-serif;
        }

        .login-by-qrcode {
            background: url(/dist/img/ic_login_qrcode.png) no-repeat;
            background-size: 40px 40px;
            width: 40px;
            height: 40px;
            float: right;
            cursor: pointer;
        }

        .login-by-pass {
            background: url(/dist/img/ic_login_pass.png) no-repeat;
            background-size: 40px 40px;
            width: 40px;
            height: 40px;
            float: right;
            cursor: pointer;
        }

        /* loading 动画 */
        .spinner {
            margin: 100px auto;
            width: 50px;
            height: 40px;
            text-align: center;
            font-size: 10px;
        }

        .spinner > div {
            background-color: #333;
            height: 100%;
            width: 6px;
            display: inline-block;

            -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
            animation: sk-stretchdelay 1.2s infinite ease-in-out;
        }

        .spinner .rect2 {
            -webkit-animation-delay: -1.1s;
            animation-delay: -1.1s;
        }

        .spinner .rect3 {
            -webkit-animation-delay: -1.0s;
            animation-delay: -1.0s;
        }

        .spinner .rect4 {
            -webkit-animation-delay: -0.9s;
            animation-delay: -0.9s;
        }

        .spinner .rect5 {
            -webkit-animation-delay: -0.8s;
            animation-delay: -0.8s;
        }

        @-webkit-keyframes sk-stretchdelay {
            0%, 40%, 100% { -webkit-transform: scaleY(0.4) }
            20% { -webkit-transform: scaleY(1.0) }
        }

        @keyframes sk-stretchdelay {
            0%, 40%, 100% {
                transform: scaleY(0.4);
                -webkit-transform: scaleY(0.4);
            }  20% {
                   transform: scaleY(1.0);
                   -webkit-transform: scaleY(1.0);
               }
        }
    </style>
</head>
<body style="width:100%;height:100%;">
<div class="wrap">
    <div class="header">
        {{ config('app.company_name') }} <span class="point"></span> {{ config('app.app_name') }}
    </div>
    <div class="content">
        <div class="login-box">
            <div class="login-box-bg"></div>
            <div class="login-form">
                <div id="use_username">
                    密码登录
                    <a target="_blank" href="{{$wx_login_url}}" class="login-by-qrcode" title="微信扫码登录"></a>
                    {{--<a onclick="document.getElementById('use_qrcode').style.display = 'block';document.getElementById('use_username').style.display = 'none';" class="login-by-qrcode"></a>--}}
                    <div class="login-input">
                        <input type="text" name="name" id="name" placeholder="工号、手机号" onkeydown="keyLogin(event)"/>
                    </div>
                    <div class="login-input">
                        <input type="password" name="pwd" id="pwd" placeholder='登录密码' onkeydown="keyLogin(event)"/>
                    </div>
                    <button id="submit">登 录</button>
                    {{--<a target="_blank" href="https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=wxa1d1b52ff8468c45&redirect_uri=http%3A%2F%2Fsalary.gm365.cc%2Fqy_code_callback">企业微信登录</a>--}}
                </div>
                <div id="use_qrcode" style="display: none;">
                    扫码登录

                    <a onclick="document.getElementById('use_qrcode').style.display = 'none';document.getElementById('use_username').style.display = 'block';" class="login-by-pass"></a>

                    <div class="spinner">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                    </div>

                    <div id="wx_reg"></div>
                </div>

                <div style="clear:both;"></div>
            </div>
            <div id="tip"></div>
        </div>

        <div style="clear:both"></div>
    </div>
</div>
<div id="container" style="position: absolute;top: 0;z-index: -1;width: 100%;height: 100%;">
    <div id="anitOut">
    </div>
</div>
<div class="footer">
    {{ config('app.copy_right') }} Copyright ©2016 All Rights Reserved.
</div>
</body>
{{--<script src="http://rescdn.qqmail.com/node/ww/wwopenmng/js/sso/wwLogin-1.0.0.js"></script>--}}
<script src="//cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<script src="/dist/js/cav.js"></script>
<script>
//    $.ajax({
//        url:'/qy_url',
//        type: 'get',
//        data: {},
//        success: function (data) {
//            if (data.code == 0) {
//                //console.log(data.result);
//                var config = data.result;
//                window.WwLogin({
//                    "id" : "wx_reg",
//                    "appid" : config.appid,
//                    "agentid" : config.agentid,
//                    "redirect_uri" : config.redirect_uri,
//                    "state" : config.state,
//                    "href" : "",
//                });
//                $("iframe").ready(function () {
//                    $(".spinner").css("display", "none")
//                    console.log("ready")
//                })
//            } else {
//                flashTip(data.msg);
//            }
//        },
//        error: function () {
//            flashTip('网络错误，请稍后再试!');
//        }
//    })



    //配置动态背景
    var victor = new Victor("container", "anitOut");
    /*  canvas背景主题设置
     var theme = [
     ["#002c4a", "#005584"],
     ["#35ac03", "#3f4303"],
     ["#ac0908", "#cd5726"],
     ["#18bbff", "#00486b"]
     ];*/

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf"]').attr('content')
        }
    });
    $('#submit').click(function () {
        login();
    })
    $('#qyLogin').click(function () {
        location.href='/qy';
//        location.href="https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=wxa1d1b52ff8468c45&redirect_uri=http%3A%2F%2Fsalary.gm365.cc%2Fqy_callback&state=gamma&usertype=member";
        //location.href = "https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid=ww1656a289fa3718c9&agentid=1000002&redirect_uri=http%3A%2F%2Fsalary.gm365.cc%2Fqy_callback&state=web_login";
    })

    //所有的input禁止输入空格
    $('input').on('input', function () {
        $(this).val($(this).val().replace(/(^\s+)|(\s+$)/g, ""));
    })

    function login() {
        if (check()) {
            var data = {name: $('#name').val(), pwd: $('#pwd').val()};
            $.ajax({
                type: 'post',
                data: data,
                success: function (data) {
                    if (data.status == 0) {
                        location.replace('/' + location.hash);
                    } else {
                        flashTip(data.msg);
                    }
                },
                error: function () {
                    flashTip('网络错误，请稍后再试!');
                }
            })
        }
    }
    //检查输入
    function check() {
        var nameObj = $('#name');
        if (nameObj.val().length == 0) {
            nameObj.focus();
            flashBorder(nameObj.parent('.login-input'));
            return false;
        }
        var pwdObj = $('#pwd');
        if (pwdObj.val().length == 0) {
            pwdObj.focus();
            flashBorder(pwdObj.parent('.login-input'));
            return false;
        }

        return true;
    }

    //出错时border闪动提示
    function flashBorder(obj) {
        obj.css('border', '1px solid #ff7575');
        setTimeout(function () {
            obj.css('border', '1px solid #cdcdcd')
        }, 1000);
    }

    //消息提示
    function flashTip(msg) {
        $('#tip').html(msg);
        setTimeout(function () {
            $('#tip').html('');
        }, 3500);
    }
    //确定键登录
    function keyLogin(event) {
        if (event.keyCode == 13) {
            login();
        }
    }
</script>
</html>