<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="user-scalable=no,width=device-width, initial-scale=1,maximum-scale=1" name="viewport">
    <meta name="format-detection" content="telephone=no"/>
    <meta content="Title" name="apple-mobile-web-app-title">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta content="{{csrf_token()}}" id="csrf_token" name="csrf_token"/>

    <title>{{ config('app.company_name') }}-{{ config('app.app_name') }}</title>
    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap-datetimepicker-standalone.css') }}">
    <link href="{{ url('css/ionicons.min.css') }}" rel="stylesheet"/>

    <script type="text/javascript" src="{{ url('js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/handlebars.min.js') }}"></script>

    <!-- alpaca -->
    <link type="text/css" href="{{ url('css/alpaca.css') }}" rel="stylesheet"/>
    <script type="text/javascript" src="{{ url('js/alpaca.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/moment-with-locales.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/jquery.price_format.min.js') }}"></script>

    <script type="text/javascript" src="{{ url('js/jquery.form.js') }}"></script>

    <style type="text/css">
        /*页面主体样式*/
        body {
            font-family: "Microsoft Yahei";
            background-color: #edf0f8;
            padding: 0px;
            margin: 0px;
        }

        .main{
            padding: 30px;
            text-align: -webkit-center;
            -webkit-overflow-scrolling:touch;
        }

        .main_head{
            text-align: -webkit-center;
            background-color: #3b67a0;
            padding: 12px;
            color: #fff;
            font-size: 25px;
            border-bottom: 6px solid #2d4e7b;
        }

        .main_box {
            width: 100%;
            max-width: 800px;
            text-align: -webkit-left;
            background-color: #fff;
            position: relative;
        }

        /*滚动条样式*/
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background-color: transparent;
        }

        /* 滚动条的滑轨背景颜色 */
        ::-webkit-scrollbar-thumb {
            background-color: #324057;
            padding: 0px 2px;
            border-radius: 5px;
        }

        /* 滑块颜色 */
        ::-webkit-scrollbar-button {
            background-color: transparent;
            height: 0px;
            width: 0px
        }

        /* 滑轨两头的监听按钮颜色 */
        ::-webkit-scrollbar-corner {
            background-color: transparent;
            height: 0px;
            width: 0px;
        }

        /*提交按钮样式*/
        .form_button{
            display: inline-block;
            white-space: nowrap;
            cursor: pointer;
            background: #3b67a0;
            color:#fff;
            padding: 10px 0;
            font-size: 18px;
            border-radius: 4px;
        }

        .form_button span{
            display: block;
            width:100px;
            height:25px;
        }

        /*表单组件样式*/
        #form_box{
            padding: 12px 0px;
            font-size: 18px;
            background-color: #fdfdfe;
        }

        input[type=text], input[type=number], button, select, textarea {
            outline: none;
            -webkit-appearance: none;
            border-radius: 0;
            padding-left: 1%;
            border: 1px solid #cccccc;
            width: 96%;
        }

        textarea{
            padding-right: 1%;
        }

        select{
            background-color: #fff;
            float: left;
            margin-bottom: 20px;
            padding-top: 3px;
            padding-right: 30px;
        }

        .control-label{
            display: -webkit-inline-box !important;
        }

        .alpaca-container-item {
            margin-top: 20px !important;
        }

        .alpaca-field-optiontree.optiontree-horizontal .optiontree .optiontree-selector{
            padding-left: 0px;
            padding-right: 10px;
        }

        .optiontree-selector{
            /* width:20%; */
            padding-right: 0px !important;
        }

        .optiontree-selector .form-group{
            padding: 0px;
        }

        .optiontree-selector .alpaca-control{
            margin: 0px;
        }

        .optiontree-selector select{
            padding-left: 5px;
            width: 95% !important;
            padding-right: 24px;
        }

        .select-icon-optiontree{
            float: right;
            margin-top: -28px;
            padding: 5px 0 0 5px;
            margin-right: 15px;
            font-size: 10px;
            color:#cccccc;
        }

        .form-group{
            padding:0 30px;
        }

        .alpaca-control {
            width: 95%;
            margin-top: 6px;
            margin-left: 15px;
        }

        .alpaca-field-text .alpaca-control {
            height: 30px;
            font-size: 15px;
        }

        .alpaca-field-select .alpaca-control {
            height: 34px;
            font-size: 15px;
        }

        .alpaca-field-date .alpaca-control {
            height: 30px;
            font-size: 15px;
        }

        .alpaca-field-textarea .alpaca-control {
            font-size: 15px;
        }

        .alpaca-field-currency .alpaca-control {
            height: 30px;
            font-size: 15px;
        }

        .required:after{
            content: " *";
            color: #ff4949;
            margin-right: 4px;
        }

        input[type=file]{
            display: block;
            width: 100px;
        }

        ::-webkit-file-upload-button {
            width: 100px;
            padding: 3px;
            line-height: 30px;
            border: 1px solid #3b67a0;
            cursor: pointer;
            background: #3b67a0;
            color:#fff;
            font-size: 15px;
            border-radius: 4px;
        }

        .file_success{
            margin-left: 128px;
            font-size: 15px;
            float: left;
            margin-top: -31px;
        }

        .radio{
            font-size: 15px;
            margin-left: 15px !important;
        }

        .radio input{
            margin-right: 8px;
        }

        .checkbox{
            font-size: 15px;
        }

        .checkbox input{
            margin-right: 4px;
        }

        .alpaca-message-invalidValueOfEnum{
            display: none;
        }

        .alpaca-message {
            color:#ff4949;
            font-size: 12px;
            padding-top: 4px
        }

        .widget_content{
            padding:0 30px;
        }

        hr {
            border-top: 1px solid #cccccc;
            margin: 0 30px;
        }

        hr {
            margin-top: 20px;
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #cccccc;
            height: 0;
            box-sizing: content-box;
        }

        .rank{
            margin-left: 15px;
            margin-top: 6px;
            font-size: 25px;
            cursor: pointer;
            -webkit-tap-highlight-color: rgba(255,255,255,0);
        }

        /* .currency_input{
            float: left;
            margin-bottom: 20px;
            width: 94% !important;
            padding-left: 3% !important;
        }
        
        .currency{
            float: left;
            margin-top: -54px;
            padding: 5px 0 0 5px;
            margin-left: 16px;
        } */

        .select-icon{
            float: right;
            margin-top: -48px;
            padding: 5px 0 0 5px;
            margin-right: 15px;
            font-size: 10px;
            color:#cccccc;
        }

        table{
            border-collapse: collapse;
            border-spacing: 0;
            vertical-align: middle;
            width: 100%;
            background-color: #fff;
            border: 1px solid #D3D3D3;
            margin-left: 15px;
            margin-top: 6px;
            width: 97%;
        }

        table td{
            border: 1px solid #D3D3D3;
            padding: 5px;
            display: table-cell;
            vertical-align: inherit;
        }

        table td .radio_div,.checkbox_div,p{
            min-height: 29px;
            line-height: 29px;
            font-size: 15px;
            font-weight: normal;
            color: #000000;
            text-align: center;
            margin:0px;
        }

        table td .text_div input{
            height: 25px;
            width:98%;
        }

        table td .rank_div{
            text-align: center;
            font-size: 25px;
            cursor: pointer;
            -webkit-tap-highlight-color: rgba(255,255,255,0);
        }

        @media screen and (max-width: 600px) {
            .main {
                padding:0px;
            }

            .form-group {
                padding: 0 12px;
            }

            .alpaca-control {
                margin-left: 0;
            }

            .radio{
                margin-left: 0 !important;
            }

            .form_button{
                width:94%;
            }

            hr {
                margin: 0 12px;
            }

            .widget_content{
                padding:0 12px;
            }

            /* .currency_input {
                width: 92% !important;
                padding-left: 5% !important;
            }
            
            .currency{
                margin-left: 0px;
            } */

            .rank{
                margin-left: 0px;
            }

            .optiontree-selector{
                padding-left: 0px !important;
            }

            .optiontree-selector select{
                padding-right: 18px;
            }

            .select-icon-optiontree{
                margin-right: 12px;
            }

            table{
                margin-left: 0px;
            }
        }
    </style>
</head>
<body>
<div class="main">
    <div class="main_box">
        <div class="main_head">{{$form_title}}</div>
        <div id="form_box"></div>
        <div style="text-align: -webkit-center;padding:3%;">
            @if ($button == 0)
                <div class="form_button">
                    <span></span>
                </div>
            @else
                <div class="form_button submit_click">
                    <span></span>
                </div>
            @endif
        </div>
    </div>
    <div class="footer" style="bottom:0px; text-align: center;color: #777;margin:20px;font-size:13px;">
        由伽马科技提供技术支持
    </div>
    <input type="hidden" id="schema" value="{{$schema}}"/>
    <input type="hidden" id="fid" value="{{$id}}"/>
</div>
</body>
<script type="text/javascript" src="{{ url('js/jweixin.js') }}"></script>
<!-- <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script> -->
<script type="text/javascript">
    $(function () {
        wx.config({
            debug: false,
            appId: '{{$js["appId"]}}',
            timestamp: "{{$js["timestamp"]}}",
            nonceStr: '{{$js["nonceStr"]}}',
            signature: '{{$js["signature"]}}',
            jsApiList: ["onMenuShareAppMessage", "onMenuShareTimeline"]
        })

        wx.ready(function () {
            wx.onMenuShareAppMessage({
                title: "{{$title}}", // 分享标题
                desc: "{{$desc}}", // 分享描述
                link: "{{$js["url"]}}", // 分享链接
                imgUrl: "{{$pic}}", // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {

                },
                cancel: function () {

                }
            });

            wx.onMenuShareTimeline({
                title: "{{$title}}", // 分享标题
                link: "{{$desc}}", // 分享链接
                imgUrl: "{{$pic}}", // 分享图标
                success: function () {

                },
                cancel: function () {

                }
            });
        });

        var full_schema = JSON.parse($('#schema').val())
        var options = full_schema.options.fields
        var files = [];
        var ranks = [];
        var tabular = [];

        for (name in options) {
            if(options[name].sourceType == 'country'){
                var label = options[name].label;
                var order = options[name].order;
                var widget_require = options[name].widget_require;
                var widget_description = options[name].hasOwnProperty("widget_description") ? options[name].widget_description : "";
                var dataroot="{{ url('js/country.json') }}";
                $.ajaxSettings.async = false;
                $.getJSON(dataroot, function(data){ 
                    options[name] = data;
                });
                options[name].label = label;
                options[name].order = order;
                options[name].widget_require = widget_require;
                options[name].widget_description = widget_description;
            }
            if (options[name].sourceType == 'multi_select' || options[name].sourceType == 'country') {
                options[name].events = {"change": function(e){
                    var select_change = $(e.target).parents(".optiontree").parent();
                    //var select_change = $("#form_box [data-alpaca-container-item-name=" + name + "]");

                    setTimeout (function(){
                        for (var i = 0; i < select_change.find(".optiontree-selector").length; i++) {
                            if(select_change.find(".optiontree-selector").eq(i).children().length == 0){
                                //select_change.find(".optiontree-selector").eq(i).hide();
                            }
                        }
                        for (var i = 0; i < select_change.find(".optiontree-selector").length; i++) {
                            if(select_change.find(".optiontree-selector").eq(i).children().length != 0){
                                $("option:contains(Choose...)").html("请选择");
                                if(select_change.find(".optiontree-selector").eq(i).find(".form-group").find("i").length == 0){
                                    select_change.find(".optiontree-selector").eq(i).find(".form-group").append('<i class="select-icon-optiontree ion-chevron-down"></i>');
                                }
                            }
                        }
                    }, 200);
                }}
            }
        }

        full_schema.postRender = function(){
            var description = $("#form_box [data-alpaca-container-item-name*=description]")
            for (var i = 0; i < description.length; i++) {
                var item = $(description[i]),
                    name = item.attr("data-alpaca-container-item-name")
                
                if (options[name] && options[name].widget_content) {
                    item.html("<pre style='white-space: pre-wrap;'>"+options[name].widget_content+"</pre>")
                    item.addClass("widget_content")
                }
            }

            for (name in options) {
                if (options[name].widget_description) {
                    $("#form_box [data-alpaca-field-name=" + name + "] .control-label").after('<span style="margin-left:1em;font-size:14px;color:#949090;">' + options[name].widget_description + '</span>')
                }

                if (options[name].widget_require) {
                    $("#form_box [data-alpaca-field-name=" + name + "] .control-label").addClass("required")
                }

                if (options[name].sourceType == "image") {
                    var path = full_schema.data[name];
                    var content = '<img src="' + path + '">'
                    if (options[name].bottom_description) {
                        var bottom_description = options[name].bottom_description;
                        content = content + '<p style="text-align: center;">' + bottom_description + '</p>'
                    }

                    var active_column = name;
                    if (options[name].widget_fullwidth) {
                        $('[data-alpaca-container-item-name=' + active_column + ']').css("padding", "4px 0")
                    } else {
                        $('[data-alpaca-container-item-name=' + active_column + ']').css("padding", "4px 20px")
                    }
                    $('[name=' + active_column + ']').html(content)
                }

                if (options[name].sourceType == "image" && options[name].order == 1) {
                    $('[data-alpaca-container-item-name=' + name + ']').css("padding-top", "0")
                    $('[data-alpaca-container-item-name=' + name + ']').find("img").css("margin-top", "-32px");
                }

                if (options[name].sourceType == 'hr') {
                    if (options[name].widget_borderstyle) {
                        var extera = options[name].widget_borderstyle == 'double' ? "border-top-width: 4px;" : "";
                        var style = 'style="border-style: ' + options[name].widget_borderstyle + ';' + extera + '"';
                    }
                    $("#form_box [data-alpaca-container-item-name=" + name + "]").html('<hr ' + style + '/>');
                }

                if (options[name].sourceType == 'number') {
                    $("#form_box [data-alpaca-container-item-name=" + name + "]").find("input").attr("type", "number");
                }

                if (options[name].sourceType == 'currency') {
                    //$("#form_box [data-alpaca-container-item-name=" + name + "]").find("input").attr("type", "number");
                    //$("#form_box [data-alpaca-container-item-name=" + name + "]").find("input").attr("class", "currency_input alpaca-control");
                    //$("#form_box [data-alpaca-container-item-name=" + name + "]").children().append('<div class="currency">¥</div>');
                    $("[name=" + name + "]").val(options[name].prefix)
                }

                if (options[name].sourceType == 'rank') {
                    ranks[name] = "";
                    var rank_content = '<div class="rank">';
                    for (var i = 0; i < Number(options[name].default_number); i++) {
                        rank_content += '<i id="' + name + '-' + i + '" data-num="' + i + '" class="ion-android-star" style="color:#f7ba2a;" data="' + options[name].max_number + '" data-id="'+name+'"></i>';
                    }
                    for (var i = Number(options[name].default_number); i < Number(options[name].max_number); i++) {
                        rank_content += '<i id="' + name + '-' + i + '" data-num="' + i + '" class="ion-android-star-outline" style="color:#c6d1de;" data="' + options[name].max_number + '" data-id="'+name+'"></i>'
                    }
                    $("#form_box [name=" + name + "]").replaceWith(rank_content + '</div>')
                }

                if (options[name].sourceType == 'multi_select' || options[name].sourceType == 'country') {
                    $("#form_box [data-alpaca-container-item-name=" + name + "]").find("input").hide();
                    var div_select = $("#form_box [data-alpaca-container-item-name=" + name + "]");
                    div_select.find(".optiontree-selector").eq(0).find(".form-group").append('<i class="select-icon-optiontree ion-chevron-down"></i>');
                    $("option:contains(Choose...)").html("请选择");
                }

                if (options[name].sourceType == 'select') {
                    $("#form_box [data-alpaca-container-item-name=" + name + "]").find(".form-group").append('<i class="select-icon ion-chevron-down"></i>');
                }

                if (options[name].sourceType == 'tabular') {
                    tabular[name] = [];
                    var tabular_content = '<table><tr><td></td>';
                    var tabular_option = options[name].tabular_option;
                    if(options[name].tabular_type == "radio" || options[name].tabular_type == "checkbox"){
                        for (var i = 0; i < tabular_option.length; i++) {
                            tabular_content += '<td><p>'+tabular_option[i]+'</p></td>';
                        }                     
                    }else if(options[name].tabular_type == "text" || options[name].tabular_type == "rank"){
                        tabular_content += '<td><p>'+options[name].tabular_text+'</p></td>';
                        tabular_option = [options[name].tabular_text]
                    }
                    tabular_content += '</tr>';
                    for (var i = 0; i < options[name].tabular_question.length; i++) {
                        tabular[name][options[name].tabular_question[i].name] = [];
                        tabular_content += '<tr><td style="font-size:15px;"><p style="text-align:left;">'+options[name].tabular_question[i].question+'</p></td>';
                        for (var k = 0; k < tabular_option.length; k++) {
                            if(options[name].tabular_type == "radio" || options[name].tabular_type == "checkbox"){
                                tabular_content += '<td><div class="'+options[name].tabular_type+'_div"><input type="'+options[name].tabular_type+'" name="'+options[name].tabular_question[i].name+'" value="'+tabular_option[k]+'" data-id="'+name+'"></div></td>';
                            }else if(options[name].tabular_type == "text"){
                                tabular_content += '<td><div class="'+options[name].tabular_type+'_div"><input type="'+options[name].tabular_type+'" name="'+options[name].tabular_question[i].name+'" data-id="'+name+'"></div></td>';
                            }else if(options[name].tabular_type == "rank"){
                                tabular[name][options[name].tabular_question[i].name] = options[name].default_number;
                                tabular_content += '<td><div class="rank_div">';
                                for (var n = 0; n < Number(options[name].default_number); n++) {
                                    tabular_content += '<i id="' + name + '-' + options[name].tabular_question[i].name + '-' + n + '" data-num="' + n + '" class="ion-android-star" style="color:#f7ba2a;" data="' + options[name].max_number + '" name="'+options[name].tabular_question[i].name+'" data-id="'+name+'"></i>';
                                }
                                for (var n = Number(options[name].default_number); n < Number(options[name].max_number); n++) {
                                    tabular_content += '<i id="' + name + '-' + options[name].tabular_question[i].name + '-' + n + '" data-num="' + n + '" class="ion-android-star-outline" style="color:#c6d1de;" data="' + options[name].max_number + '" name="'+options[name].tabular_question[i].name+'" data-id="'+name+'"></i>'
                                }
                                tabular_content += '</div></td>';
                            }
                        }
                        tabular_content += '</tr>';
                    }
                    tabular_content += '</table>';
                    $("#form_box [name=" + name + "]").replaceWith(tabular_content)
                }
            }

            $(".alpaca-image-display").parents(".form-group").css("padding", "0 0");
            $(".alpaca-image-display").css("margin-left", "0px");
            $(".alpaca-image-display").css("width", "100%");
            $(".alpaca-image-display").find("img").css("width", "100%");

            $("select").css("width", "97%");
            $("input[type=file]").attr("name","file");

            $("table input").bind("change", function (e){
                var type = $(this).attr("type");
                var id = $(this).attr("data-id");
                var input_name = $(this).attr("name");                
                if(type == "radio" || type =="text"){
                    var val = $(this).val();
                    tabular[id][input_name] = val;
                }else if(type == "checkbox"){                 
                    var r = $("input[name="+input_name+"][data-id="+id+"]");
                    var table = []
                    for(var i = 0; i < r.length; i++){
                        if(r[i].checked){
                            table.push($(r[i]).val())
                        }
                    }
                    tabular[id][input_name] = table;
                }
            })

            $("table .rank_div i").bind("click", function (e){
                var id = $(this).attr("data-id");
                var rank_name = $(this).attr("name");    
                var rank_num = $(this).attr("data-num");
                var rank_num_max = $(this).attr("data");
                for (var i = 0; i <= Number(rank_num); i++) {
                    $("#" + id + '-' + rank_name + '-' + i).attr("class", "ion-android-star")
                    $("#" + id + '-' + rank_name + '-' + i).css("color", "#f7ba2a")
                }
                for (var i = (Number(rank_num) + 1); i < Number(rank_num_max); i++) {
                    $("#" + id + '-' + rank_name + '-' + i).attr("class", "ion-android-star-outline")
                    $("#" + id + '-' + rank_name + '-' + i).css("color", "#c6d1de")
                }
                tabular[id][rank_name] = (Number(rank_num) + 1);
            })

            $(".rank i").bind("click", function (e) {
                var name = $(this).attr("data-id");
                var rank_num = $(this).attr("data-num");
                var rank_num_max = $(this).attr("data");
                for (var i = 0; i <= Number(rank_num); i++) {
                    $("#" + name + '-' + i).attr("class", "ion-android-star")
                    $("#" + name + '-' + i).css("color", "#f7ba2a")
                }
                for (var i = (Number(rank_num) + 1); i < Number(rank_num_max); i++) {
                    $("#" + name + '-' + i).attr("class", "ion-android-star-outline")
                    $("#" + name + '-' + i).css("color", "#c6d1de")
                }
                ranks[name] = (Number(rank_num) + 1)
            });

            $("input[type=file]").change(function (e) {
                var input_name = $("#" + e.currentTarget.id).parent().parent().attr("data-alpaca-container-item-name");
                $(this).wrap("<form id='" + input_name + "' action='./upload' method='post' enctype='multipart/form-data'></form>");
                $("#" + input_name).ajaxSubmit({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data.code == 0){
                            files.push({"name":input_name,"file":data.result.name})
                            $("#" + e.currentTarget.id).parents(".form-group").find(".file_success").hide();
                            $("#" + e.currentTarget.id).parents(".form-group").append('<div class="file_success">' + data.result.original +' 上传成功！</div>')                            
                        }else{
                            $("#" + e.currentTarget.id).parents(".form-group").find(".file_success").hide();
                            $("#" + e.currentTarget.id).parents(".form-group").append('<div class="file_success">' + data.msg +',请重新上传！</div>')                            
                        }
                    },
                    error: function (data) {
                    }
                });
                $(this).unwrap();
                return false;
            });
        }

        $("#form_box").alpaca(full_schema);

        if(full_schema.hasOwnProperty("submit_button_name")){
            $(".form_button span").html(full_schema.submit_button_name);
        }

        $(".form_button").mouseover(function(){
            $(".form_button").css("background","#547ae0");
        });
        $(".form_button").mouseout(function(){
            $(".form_button").css("background","#3b67a0");
        });

        $(".submit_click").bind("click", function(event){
            set_submit(event, files, ranks, tabular);
        });
    });

    function set_submit(event, files, ranks, tabular) {
        var submit = $("#form_box").alpaca("get").getValue();
        var form_schema = JSON.parse($('#schema').val());
        var form_id = $('#fid').val();

        var a = 0;
        var b = 0;
        $.each(form_schema.options.fields, function(k, v){
            if(v.widget_require){
                a++
                if(submit[k] && submit[k].length > 0 ){
                    b++
                }
            }
        })

        if(a != b){
            alert("请填写完成后再提交！")
            return false;
        }
        $(this).unbind(event);

        $.each(submit, function(i, n) {
            $.each(form_schema.options.fields, function(k, v){
                if(i == k){
                    if(v.sourceType == "image"){
                        delete submit[i];
                    }else{
                        submit[i] = {"value":n,"type":v.sourceType,"number":v.order,"enum": v.hasOwnProperty("optionLabels") ? v.optionLabels : [],"title": v.label,"developer_options": v.hasOwnProperty("developer_options") ? v.developer_options : ""}
                    }
                }
            })
        });

        $.each(form_schema.options.fields, function(k, v){
            if(v.sourceType == "file"){
                $.each(files, function(a, b){
                    if(b.name && b.name == k){
                        submit[k] = {"value":b.file,"type":v.sourceType,"number":v.order,"enum": v.hasOwnProperty("optionLabels") ? v.optionLabels : [],"title": v.label,"developer_options": v.hasOwnProperty("developer_options") ? v.developer_options : ""}
                    }
                })
            }
            if(v.sourceType == "rank"){
                submit[k] = {"value":Number(v.default_number),"type":v.sourceType,"number":v.order,"enum": v.hasOwnProperty("optionLabels") ? v.optionLabels : [],"title": v.label,"developer_options": v.hasOwnProperty("developer_options") ? v.developer_options : ""}
                for(var r in ranks){
                    if(r == k){
                        submit[k] = {"value":Number(ranks[r]),"type":v.sourceType,"number":v.order,"enum": v.hasOwnProperty("optionLabels") ? v.optionLabels : [],"title": v.label,"developer_options": v.hasOwnProperty("developer_options") ? v.developer_options : ""}
                    }
                }
            }
            if(v.sourceType == "tabular"){
                for(var i in tabular){
                    if(i == k){
                        var value = {}
                        for(var m in tabular[i]){
                            value[m] = tabular[i][m]
                        }
                        submit[k] = {"value":value,"type":v.sourceType,"tabular_type":v.tabular_type,"number":v.order,"enum": v.hasOwnProperty("optionLabels") ? v.optionLabels : [],"title": v.label,"developer_options": v.hasOwnProperty("developer_options") ? v.developer_options : ""}
                    }
                }
            }
            if(v.sourceType == "multi_select" || v.sourceType == "country"){
                var select = $("#form_box [data-alpaca-container-item-name=" + k + "]").find("select");
                var select_val = "";
                $.each(select, function(a,b){
                    select_val += $(b).val() + "-";
                })
                submit[k] = {"value":select_val,"type":v.sourceType,"number":v.order,"enum": v.hasOwnProperty("optionLabels") ? v.optionLabels : [],"title": v.label,"developer_options": v.hasOwnProperty("developer_options") ? v.developer_options : ""}
            }
        })

        form_schema.submit = submit
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            },
            type: "post",
            url: "../forms/submit",
            data: {"form_schema": JSON.stringify(form_schema),"form_id":form_id},
            dataType: "json",
            success: function (data) {
                if(data.code == 0){
                    window.location.href="../warning/1";
                }else{
                    window.location.href="../warning/0";
                }
            },
            error: function(){
                window.location.href="../warning/0";
            }
        });
    }
</script>
</html>
