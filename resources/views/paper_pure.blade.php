<!DOCTYPE html>
<html>
<head>
    <title>{{ $schema->title }}</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }
        .page {
            position: relative;
            height: {{ $schema->height }}px;
        }

        .title {
            text-align: center;
            font-size: 22px;
            padding: 8px;
            cursor: pointer;
            color: #333;
            margin: 0;
        }

        .scope_title {
            padding: 18px 4px 4px 24px;
            margin: 0;
            border: 1px dashed transparent;
        }

        .page .question_title img {
            height: 20px;
            max-width: 100%;
        }

        .page .question_title img[width] {
            height: auto;
        }

        .page p {
            margin: 0;
            word-break: break-word;
        }

        .page footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button{
            -webkit-appearance: none !important;
            margin: 0; 
        }

        .question_title p:first-child {
            display: inline;
        }

        .flex-left {
            page-break-before: always;
        }

        blank {
            text-decoration: underline;
        }

        .one_answer {
            padding-left: 24px;
            display: inline-block;
        }

@charset "UTF-8";/*! Mobi.css v2.0.0-beta.1 http://getmobicss.com */html{box-sizing:border-box}*,::after,::before{box-sizing:inherit}html{font-size:10px;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;text-size-adjust:100%}body{background-color:#fff;color:#333;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Hiragino Sans GB","Microsoft Yahei","微软雅黑",Arial,Helvetica,STHeiti,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-size:16px;margin:0}h1,h2,h3,h4,h5,h6{margin:30px 0 0}h1,h2,h3,h4,h5,h6{font-weight:600}h1{font-size:32px}h2{font-size:26px}h3{font-size:22px}h4{font-size:20px}h5{font-size:18px}h6{font-size:16px}a{color:#2680d9;text-decoration:none;-webkit-text-decoration-skip:objects}a:active,a:hover{text-decoration:underline}b,dt,strong{font-weight:600}code,kbd,samp{background-color:#f2f2f2;font-family:Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:85%;padding:.2em .3em}pre{background-color:#f2f2f2;font-family:Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:13px;line-height:1.2;overflow:auto;-webkit-overflow-scrolling:touch;padding:15px}pre code{background-color:transparent;font-size:13px;padding:0}blockquote{border-left:5px solid #ddd;color:#777;padding-left:15px}ol,ul{padding-left:30px}dd,dt,ol ol,ol ul,ul ol,ul ul{margin:0}hr{border:0;border-top:1px solid #ddd}small{font-size:85%}sub,sup{font-size:85%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.2em}sup{top:-.4em}address,time{font-style:normal}mark{background-color:#ff0;color:#333;padding:0 .2em}rt{font-size:60%}abbr[title]{border-bottom:0;text-decoration:underline;text-decoration:underline dotted}audio:not([controls]){display:none;height:0}img{max-width:100%;vertical-align:middle}audio,video{max-width:100%}figcaption{color:#777;font-size:85%}[role=button]{cursor:pointer}[role=button],a,area,button,input,label,select,summary,textarea{-ms-touch-action:manipulation;touch-action:manipulation}button,input,select,textarea{font:inherit}::-webkit-file-upload-button,[type=reset],[type=submit],button,html [type=button]{-webkit-appearance:button;appearance:button}[type=search]{-webkit-appearance:none;-moz-appearance:none;appearance:none}::-webkit-file-upload-button{font:inherit}[hidden]{display:none}fieldset{border:1px solid #ddd;margin:15px 0 0;padding:0 15px 15px}legend{padding:0 .2em}optgroup{color:#777;font-style:normal;font-weight:400}option{color:#333}progress{max-width:100%}.container,.container-fluid,.container-wider{overflow:hidden;padding:0 15px 15px;-webkit-box-flex:1;-webkit-flex-grow:1;-ms-flex-positive:1;flex-grow:1}.container{max-width:800px}.container-wider{max-width:1200px}.flex-bottom,.flex-center,.flex-left,.flex-middle,.flex-right,.flex-top,.flex-vertical{-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-flow:row nowrap;-ms-flex-flow:row nowrap;flex-flow:row nowrap;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex}.flex-bottom,.flex-center,.flex-left,.flex-middle,.flex-right,.flex-top,.flex-vertical.flex-bottom,.flex-vertical.flex-center,.flex-vertical.flex-left,.flex-vertical.flex-middle,.flex-vertical.flex-right,.flex-vertical.flex-top{-webkit-box-align:stretch;-webkit-align-items:stretch;-ms-flex-align:stretch;align-items:stretch;-webkit-box-pack:start;-webkit-justify-content:flex-start;-ms-flex-pack:start;justify-content:flex-start}.flex-center,.flex-vertical.flex-middle{-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center}.flex-right,.flex-vertical.flex-bottom{-webkit-box-pack:end;-webkit-justify-content:flex-end;-ms-flex-pack:end;justify-content:flex-end}.flex-top,.flex-vertical.flex-left{-webkit-box-align:start;-webkit-align-items:flex-start;-ms-flex-align:start;align-items:flex-start}.flex-middle,.flex-vertical.flex-center{-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center}.flex-bottom,.flex-vertical.flex-right{-webkit-box-align:end;-webkit-align-items:flex-end;-ms-flex-align:end;align-items:flex-end}.units-gap{margin-left:-7.5px;margin-right:-7.5px}.units-gap>.unit,.units-gap>.unit-0,.units-gap>.unit-1,.units-gap>.unit-1-2,.units-gap>.unit-1-3,.units-gap>.unit-1-4,.units-gap>.unit-1-on-mobile,.units-gap>.unit-2-3,.units-gap>.unit-3-4{padding-left:7.5px;padding-right:7.5px}.units-gap-big{margin-left:-15px;margin-right:-15px}.units-gap-big>.unit,.units-gap-big>.unit-0,.units-gap-big>.unit-1,.units-gap-big>.unit-1-2,.units-gap-big>.unit-1-3,.units-gap-big>.unit-1-4,.units-gap-big>.unit-1-on-mobile,.units-gap-big>.unit-2-3,.units-gap-big>.unit-3-4{padding-left:15px;padding-right:15px}.unit{-webkit-flex-basis:0;-ms-flex-preferred-size:0;flex-basis:0;-webkit-box-flex:1;-webkit-flex-grow:1;-ms-flex-positive:1;flex-grow:1;max-width:100%}.unit-1,.unit-1-2,.unit-1-3,.unit-1-4,.unit-1-on-mobile,.unit-2-3,.unit-3-4{-webkit-flex-shrink:0;-ms-flex-negative:0;flex-shrink:0}.unit-1{-webkit-flex-basis:100%;-ms-flex-preferred-size:100%;flex-basis:100%;max-width:100%}.unit-1-2{-webkit-flex-basis:50%;-ms-flex-preferred-size:50%;flex-basis:50%;max-width:50%}.unit-1-3{-webkit-flex-basis:33.33%;-ms-flex-preferred-size:33.33%;flex-basis:33.33%;max-width:33.33%}.unit-2-3{-webkit-flex-basis:66.67%;-ms-flex-preferred-size:66.67%;flex-basis:66.67%;max-width:66.67%}.unit-1-4{-webkit-flex-basis:25%;-ms-flex-preferred-size:25%;flex-basis:25%;max-width:25%}.unit-3-4{-webkit-flex-basis:75%;-ms-flex-preferred-size:75%;flex-basis:75%;max-width:75%}.flex-vertical{-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column}.flex-vertical>.unit,.flex-vertical>.unit-0,.flex-vertical>.unit-1,.flex-vertical>.unit-1-2,.flex-vertical>.unit-1-3,.flex-vertical>.unit-1-4,.flex-vertical>.unit-1-on-mobile,.flex-vertical>.unit-2-3,.flex-vertical>.unit-3-4{max-width:none}.flex-vertical>.unit-1{max-height:100%}.flex-vertical>.unit-1-2{max-height:50%}.flex-vertical>.unit-1-3{max-height:33.33%}.flex-vertical>.unit-2-3{max-height:66.67%}.flex-vertical>.unit-1-4{max-height:25%}.flex-vertical>.unit-3-4{max-height:75%}.flex-wrap{-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap}@media (max-width:767px){.unit-1-on-mobile{-webkit-flex-basis:100%;-ms-flex-preferred-size:100%;flex-basis:100%;max-width:100%}.flex-vertical>.unit-1-on-mobile{max-height:100%}}.top-gap-big{margin-top:30px!important}.top-gap{margin-top:15px!important}.top-gap-0{margin-top:0!important}@media (max-width:767px){.hide-on-mobile{display:none!important}}@media (min-width:768px){.show-on-mobile{display:none!important}}.table{background-color:#fff;border:0;border-collapse:collapse;border-spacing:0;width:100%}.table caption{caption-side:bottom;color:#777;font-size:85%;padding:5px;text-align:left}.table td,.table th{border:0;border-bottom:1px solid #ddd;padding:5px;text-align:left}.table th{background-color:#f2f2f2;font-weight:600}.btn{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border:1px solid #ddd;border-radius:3px;color:#333;cursor:pointer;display:inline-block;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Hiragino Sans GB","Microsoft Yahei","微软雅黑",Arial,Helvetica,STHeiti,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-size:16px;line-height:1.25;margin:15px 0 0;padding:5px 10px;text-align:center}.btn:active,.btn:hover{background-color:#f2f2f2;text-decoration:none}.btn[disabled]{cursor:default;opacity:.5;pointer-events:none}.btn-primary{background-color:#2680d9;border-color:#2680d9;color:#fff}.btn-primary:active,.btn-primary:hover{background-color:#2273c3}.btn-primary[disabled]{cursor:default;opacity:.5;pointer-events:none}.btn-danger{background-color:#db5757;border-color:#db5757;color:#fff}.btn-danger:active,.btn-danger:hover{background-color:#d74242}.btn-danger[disabled]{cursor:default;opacity:.5;pointer-events:none}.btn-block{display:block;width:100%}.form{margin:0}.form label{border:1px solid transparent;cursor:pointer;display:block;line-height:1.25;margin-top:15px;padding-bottom:5px;padding-top:5px}.form [type=email],.form [type=number],.form [type=password],.form [type=search],.form [type=tel],.form [type=text],.form [type=url],.form select,.form textarea{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border:1px solid #ddd;border-radius:3px;color:#333;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Hiragino Sans GB","Microsoft Yahei","微软雅黑",Arial,Helvetica,STHeiti,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-size:16px;padding:5px;display:block;line-height:1.25;margin:15px 0 0;width:100%}.form [type=email]:focus,.form [type=number]:focus,.form [type=password]:focus,.form [type=search]:focus,.form [type=tel]:focus,.form [type=text]:focus,.form [type=url]:focus,.form select:focus,.form textarea:focus{border-color:#2680d9;outline:0}@media (max-width:767px){.form [type=date],.form [type=datetime-local],.form [type=month],.form [type=time],.form [type=week]{margin:15px 0 0}}@media (min-width:768px){.form [type=date],.form [type=datetime-local],.form [type=month],.form [type=time],.form [type=week]{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border:1px solid #ddd;border-radius:3px;color:#333;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Hiragino Sans GB","Microsoft Yahei","微软雅黑",Arial,Helvetica,STHeiti,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-size:16px;padding:5px;display:block;line-height:1.25;margin:15px 0 0;width:100%}.form [type=date]:focus,.form [type=datetime-local]:focus,.form [type=month]:focus,.form [type=time]:focus,.form [type=week]:focus{border-color:#2680d9;outline:0}}.form [type=checkbox],.form [type=radio]{cursor:pointer;margin:0 5px 0 0}.form select{cursor:pointer}.form [type=file],.form [type=range]{display:block;line-height:1.25;margin:15px 0 0;width:100%;border-top:1px solid transparent;border-bottom:1px solid transparent;cursor:pointer;padding-bottom:5px;padding-top:5px}.form [type=color],.form [type=image]{cursor:pointer;display:block;margin:15px 0 0}.form [disabled]{cursor:default;opacity:.5;pointer-events:none}.form [readonly]{background-color:#f2f2f2}.scroll-view{overflow:auto;-webkit-overflow-scrolling:touch}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.text-muted{color:#777}.text-primary{color:#2680d9}.text-danger{color:#db5757}a.text-danger,a.text-muted,a.text-primary{text-decoration:underline}.text-small{font-size:85%}
    </style>
</head>
<body>
    <div>
        @foreach ($pages as $page)
        @if ($loop->iteration % 2 == 1)
            <!-- 页面左侧的内容，同时开启一行 row -->
        <div class="flex-left">
        <div style="height: {{ $schema->height }}px;" class="page">
            <div style="width: {{ $schema->width }}px;">
        @endif
        @if ($loop->iteration % 2 == 0)
        <div style="height: {{ $schema->height }}px;" class="page">
            <div style="width: {{ $schema->width }}px;">
        @endif
            @foreach ($page as $item)
                @if ($item->type == 'title')
                    <h1 class="title">
                        {{ $schema->title }}
                    </h1>
                @elseif ($item->type == 'scope')
                    <p class="scope_title">
                        {{ $item->title }}
                    </p>
                @elseif ($item->type == 'question')
                    <div style="padding: 8px 4px 8px 24px;">
                        {{ $item->order }}、<span class="question_title">{!! $item->title !!}</span>
                        @if (isset($item->check_question) && $item->check_question)
                            <span style="float: right;">（&nbsp;&nbsp;&nbsp;）</span>
                        @endif
                    </div>
                @elseif ($item->type == 'attachment')
                    <div>
                        {!! $item->attachment !!}
                    </div>
                @elseif ($item->type == 'option')
                    <span class="one_answer question_title">
                        <!-- 选项 -->
                        {{ $item->answer->value }}、{!! $item->answer->text !!}
                    </span>
                @elseif ($item->type == 'answer_row')
                    <div style="height: {{ $schema->row_height }}px;"></div>
                @elseif ($item->type == 'br')
                    <div class="brtest"></div>
                @endif
            @endforeach

            <footer style="height: {{ $schema->footer->height }}px; font-size: 14px;}">
                {{ sprintf($schema->footer->title, $loop->iteration, $loop->count) }}
            </footer>
        </div>
        </div>
        @if ($loop->iteration % 2 == 0)
            <!-- 结束一行 row -->
        </div>
        @endif
        @endforeach
    </div>
</body>
</html>
