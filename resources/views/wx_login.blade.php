<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="{{csrf_token()}}" id="csrf_token" name="csrf_token"/>

    <title>{{ config('app.company_name') }}-{{ config('app.app_name') }}</title>
    {{--<link href="/admin/css/slide-unlock/slide-unlock.css" rel="stylesheet" type="text/css"/>--}}
    <style type="text/css">
        body {
            font-family: "Microsoft Yahei";
            text-align: center;
        }

        .main_box {
            width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            /*position: relative;*/

        }

        .banner_box > img {
            border-top-right-radius: 5px;
            border-top-left-radius: 5px;
        }

        .banner_box {
            width: 100%;
            height: 250px;
            background-color: #FFFFCC;
            border-top-right-radius: 5px;
            border-top-left-radius: 5px;
            position: absolute;
            top: 0px;
            left: 0px;
            box-shadow: 5px 5px 3px #ddd;
        }

        .notice_box {
            width: 579px;
            height: 390px;
            background-color: white;
            padding: 20px 40px;
            position: absolute;
            top: 250px;
            left: 0px;
            border-right: 1px dashed #FC611F;
            border-bottom-left-radius: 5px;
            box-shadow: 5px 5px 3px #ddd;
        }

        .login_box {
            width: 300px;
            height: 390px;
            background-color: white;
            padding: 20px;
            position: absolute;
            top: 250px;
            right: 500px;
            border-bottom-right-radius: 5px;
            box-shadow: 5px 5px 3px #ddd;
        }

        .main {
            /*background-image: url('/img/admin/login.jpg');
            height: 600px;
            position: absolute;
            z-index: 9;
            top: 70px;
            left: 0;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            width: 100%;*/
        }

        .login_account_box {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0px;
            right: 0px;
            left: 0px;
            bottom: 0px;
            z-index: 999;
            background: rgba(0, 0, 0, 0.8);
            display: none;
        }

        .login_account_box > #login_account {
            background-color: #fff;
            width: 300px;
            padding: 20px;
            margin: 50px auto;
            position: relative;
        }

        #login_account input {
            width: 220px;
            height: 32px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding-left: 32px;
            color: #666;
        }

        #login_account #account {
            background: url("/img/admin/icon-user.png") no-repeat;
            background-size: 20px;
            background-position: 2% 50%;
        }

        #login_account #pwd {
            background: url("/img/admin/icon-pwd.png") no-repeat;
            background-size: 20px;
            background-position: 2% 50%;
        }

        #login_account .submit {
            display: inline-block;
            text-align: center;
            width: 255px;
            height: 42px;
            line-height: 42px;
            background-color: #ddd;
            color: white;
            border-radius: 4px;
            margin-top: 30px;
            font-weight: 700;
            border: 0px;
            cursor: pointer;
            font-size: 16px;
        }

        #login_account .btnClose {
            width: 25px;
            height: 25px;
            background: #fff;
            border: solid 1px #999;
            color: #666;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 16px;
            text-align: center;
            border-radius: 50%;
            cursor: pointer;
        }

        .tips {
            margin-top: 10px;
            display: inline-block;
            color: red;
        }

        .footer a {
            text-decoration: none;
            color: #777;
            font-size: 15px;
            padding: 0px 5px;
        }

        .footer a:hover {
            color: #FC611F;
        }

        .notice_box > ul {
            list-style: none;
            padding: 10px 0px 0px 0px;
            margin: 0px;
        }

        .notice_box > ul > li > a:hover {
            color: #FC611F;
        }

        .notice_box > ul > li {
            font-size: 15px;
            color: #555;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            padding: 2px 0px;
        }

        .notice_box > ul > li > a {
            color: #555;
            font-size: 15px;
            text-decoration: none;
        }

        .pagination > a:hover {
            background-color: #FC611F;
            border-color: #E85515;
        }

        .pagination > a {
            display: inline-block;
            background-color: #10AEFF;
            border: 1px solid #0AA5F0;
            width: 22px;
            height: 22px;
            text-align: center;
            text-decoration: none;
            border-radius: 3px;
        }

        .pagination > a > img {
            position: relative;
            top: 1px;
            width: 14px;
        }
    </style>
</head>
<body style="background-color: #ececec;padding:0;margin: 0px;">
<div class="main">
    <div class="main_box">
        <div class="login_box">
            <p style="text-align: center;">微信扫码登录，安全快捷</p>
            <div id="login_qcode">
                <div id="login_container" style="height:300px;">
                    <div style="border: 1px solid #E2E2E2;text-align: center;width: 204px;height: 204px;margin-left: auto;margin-right: auto;"
                         id="guid_image">
                    </div>
                    <div style="text-align: center;font-size: 13px;line-height: 0.4">
                        <p>请使用微信扫描二维码登录</p>
                    </div>
                </div>
                <div style="text-align: center;">
                    <img src="/img/admin/icon-tips1.jpg"/>
                </div>
            </div>
        </div>
    </div>
    <div class="footer"
         style="height: 100px;width: 100%;position: absolute;top: 830px;background-color: #ddd; text-align: center;line-height: 100px; color: #777;">
        <a href="javascript:void(0)">
            © 2016 GammaInfo.Inc 版权所有
        </a>
        |
        <a href="javascript:void(0)">
            豫ICP备14025783号
        </a>
        |
        <a href="/login?admin=1" id="adminLogin">
            管理员登录
        </a>
    </div>
    <input type="hidden" id="wx_login_notify_url" value="{{$notify_url}}"/>
    <input type="hidden" id="wx_auth_code" value="{{$auth_code}}"/>
</div>
{{--<div class="login_account_box">--}}
    {{--<div id="login_account">--}}
        {{--<div style="text-align: center;">--}}
            {{--<h3 style="color:#666;text-align: left;padding-left:20px;">--}}
                {{--管理员登录--}}
            {{--</h3>--}}
            {{--<input id="account" placeholder="用户名" type="text"/>--}}
            {{--<input id="pwd" placeholder="密码" type="password"/>--}}

            {{--<div id="slider">--}}
                {{--<div id="slider_bg">--}}
                {{--</div>--}}
                {{--<span id="label">--}}
                            {{-->>--}}
                        {{--</span>--}}
                {{--<span id="labelTip">--}}
                            {{--拖动滑块验证--}}
                        {{--</span>--}}
            {{--</div>--}}
            {{--<button class="submit" disabled="disabled">--}}
                {{--登录--}}
            {{--</button>--}}
            {{--<span class="btnClose">--}}
                {{--&times;--}}
            {{--</span>--}}
            {{--<span class="tips">--}}
                    {{--</span>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
</body>
<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js">
</script>
{{--<script src="/admin/css/slide-unlock/slide-unlock.js" type="text/javascript">--}}
{{--</script>--}}
<script type="text/javascript">

    $(function () {
        var url = $('#wx_login_notify_url').val();
        var code= $('#wx_auth_code').val();
        console.log(code);
//        console.log(url);
        // 更换新的 guid 和 qrcode
        function generate_image() {

            $.get(url+"/api/guid_qrcode", function (response) {
//                alert(response);
//                console.log(response);
                var guid = response.guid;
                $("#guid_image").html(response.qrcode);
//                // 轮询判断是否登录成功
                var queryID = setInterval(function () {
                    $.get(url+'/api/check_guid', {guid: guid,code:code}, function(response){
                        if (response.status == 1) {
                            // 成功

                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                                },
                                url     : '/auth_callback',
                                type    : 'post',
                                data    : {openid: response.openid},
                                success : function(data){
                                    if(data.status == 0){
                                        location.replace('/')
                                    }else{
                                        console.log(data.msg);
                                    }
                                },
                                error   : function(){
                                    alert('网络错误，请稍后再试');
                                }
                            })

                            //window.location.href = url+"/api/wechat/callback?guid=" + response.guid+'&callback='+location.host+'/api/auth_callback';
//                            var callback = location.host+'/api/auth_callback';
//                            $.get(url+"/api/wechat/callback",{guid:response.guid,callback:callback}, function(res){
//                                console.log(res);
//                                window.location.href = "/api/auth_callback?user="+JSON.stringify(res.result);
//                            });

                            clearInterval(queryID)
                        } else if (response.status == 2) {
                            // 失败
                            clearInterval(queryID);
                            var time = 3;
                            for (var i = time; i > 0; i--) {
                                setTimeout(function() {
                                    $("#guid_image").html("<div style='margin-top: 8px'><p>您已取消扫码</p><p>" + i + "秒后为您重新生成二维码</p></div>")
                                }, (time - i) * 1000)
                            }
                            setTimeout(generate_image, 3000)
                        }
                    });
                }, 2000);
            })
        }

        generate_image()
    });
</script>
</html>
