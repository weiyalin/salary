<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf" content="{{csrf_token()}}">
    <title>{{ $paper->name }}</title>
    {{--<link href="//cdn.bootcss.com/meyer-reset/2.0/reset.min.css" rel="stylesheet">--}}
    <script src="{{$host}}/dist/js/jquery.min.js"></script>
    <script src="{{$host}}/dist/js/JsBarcode.code128.min.js"></script>

    <style>
        html, body {
            font-size: 14px;
        }

        .as-container {
            font-size: 3.0rem;
            color: #333;
            position: relative;
        }

        .as-title-container {
            font-size: 4.5rem;
            font-weight: bold;
        }

        .as-small-font {
            display: inline-block;
            font-size: 3.0rem;
            font-weight: bold;
            /*transform: scale(0.8);*/
        }

        .as-surface {
            /*一面,一张纸的正面或反面*/
            min-height: 200px;
            position: relative;
            -webkit-text-size-adjust: none;
            box-sizing: border-box;
        }

        .as-main-color {
            color: #c895b4;
        }

        table.as-student td {
            border: solid 1px #c895b4;
        }

        td.as-td-label {
            width: 25px;
        }

        table.as-card-num-container, table.as-student {
            border: solid 1px #c895b4;
            text-align: center;
            border-collapse: collapse;
        }

        table.as-card-num-container thead td {
            border: solid 1px #c895b4;
            padding: 0;
            width: 14px;
            height: 20px;
        }

        table.as-card-num-container td {
            border-left: solid 1px #c895b4;
            border-right: solid 1px #c895b4;
        }

        .as-card-num-write {
            /*background: #fff;*/
            border-top: solid 1px #c895b4;
            border-bottom: solid 1px #c895b4;
        }

        .hidden {
            visibility: hidden;
        }

        .as-bracket:before {
            content: '[';
            font-weight: normal;
            /*transform: scale(0.8);*/
        }

        .as-bracket:after {
            content: ']';
            font-weight: normal;
            /*transform: scale(0.8);*/
        }

        .as-line:after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 1px;
            height: 100%;
            background: #000;
            /*border-right: solid 1px #c895b4;*/
        }

        .as-line:first-child:after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 0;
            height: 100%;
            background: #f0ad4e;
        }

        .as-line:last-child {
            border-right: none;
        }

        .as-grid-row-selected, .as-grid-col-selected {
            background: #f0ad4e !important;
        }

        .page-break {
            page-break-before: always;
        }

        .edui-editor-toolbarbox {
            /*display: none;*/
        }

        .as-answer p {
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 3.2rem;
        }
    </style>
</head>
<body>
<div id="answer-sheet-container" class="as-container">

</div>

<script>
    String.prototype.format = function (args) {
        var result = this;
        if (arguments.length > 0) {
            if (arguments.length == 1 && typeof (args) == "object") {
                for (var key in args) {
                    if (args[key] != undefined) {
                        var reg = new RegExp("({" + key + "})", "g");
                        result = result.replace(reg, args[key]);
                    }
                }
            }
            else {
                for (var i = 0; i < arguments.length; i++) {
                    if (arguments[i] != undefined) {
                        var reg = new RegExp("({)" + i + "(})", "g");
                        result = result.replace(reg, arguments[i]);
                    }
                }
            }
        }
        return result;
    }
</script>

<script>

    var AS = {};//答题卡
    var Paper = {};
    var Surface = {
        createNew: function (surfaceDom, asDom) {
            var surface = {};
            surface.width = AS.width;//
            surface.height = AS.height;//
            surface.asDom = asDom;
            surface.cols = AS.cols;//每面分几栏
            surface.colSpacing = AS.track_x.width;//栏之前的间隔
            surface.contentRegion = {};//内容区域,去除定位点、导引道
            surface.dom = surfaceDom;
            surface.locPointRegion = {};//四角定位点内框区域
            surface.track_x_list = [];//保存所有track_x块
            surface.track_y_list = [];//保存所有track_y块

            surface.init = function () {
                //step 1
                this.initLocationPoint();

                //step 2
                this.initContentRegion();

                //step 3
                this.initTrackY();
                this.initTrackX();

                if (AS.DEBUG) {
                    this.buildHelpGrid();

                    this.initPageSplitter();
                }
            };

            surface.initContentRegion = function () {
                //console.log('initContentRegion-start');
                //页面四角定位点内侧可用区域大小
                var left = this.locPointRegion.x;
                var top = this.locPointRegion.y + AS.track_y.spacing;//右上角定位点与track_y的间距

                var preWidth = this.locPointRegion.width - AS.track_y.width;//预设宽度,四角定位点内的宽度减掉track_y的宽度
                var trackXBlockItemWidth = AS.track_x.width + AS.track_x.spacing;//track_x的宽度
                var trackXBlockCount = Math.floor(preWidth / trackXBlockItemWidth);
                var realWidth = trackXBlockCount * trackXBlockItemWidth - AS.track_x.spacing;//实际宽度

                var preHeight = this.locPointRegion.height - AS.footer.height;
                var trackYBlockItemHeight = AS.track_y.height + AS.track_y.spacing;
                var trackYBlockCount = Math.floor(preHeight / trackYBlockItemHeight);
                var realHeight = trackYBlockCount * trackYBlockItemHeight;//实际高度
                realHeight -= trackYBlockItemHeight;

                this.contentRegion = {
                    x: left,
                    y: top,
                    width: realWidth,
                    height: realHeight
                };

                if (AS.DEBUG) {
//                    $('<div></div>').css({
//                        position: 'absolute',
//                        left: this.contentRegion.x,
//                        top: this.contentRegion.y,
//                        width: this.contentRegion.width,
//                        height: this.contentRegion.height,
//                        background: '#f9f8e4'
//                    }).appendTo(this.dom);
                }
                //设置surface大小
                this.dom.css({
                    width: this.width,
                    height: this.height
                });
                //console.log('initContentRegion-end', this.contentRegion);
            };

            //定位点
            surface.initLocationPoint = function () {
                //console.group('initLocationPoint-start');

                //left-top
                this.dom.append($('<div></div>').css({
                    width: AS.location_point.size,
                    height: AS.location_point.size,
                    position: 'absolute',
                    background: '#000',
                    left: AS.location_point.margin,
                    top: AS.location_point.margin,
                }));

                //left-bottom
                this.dom.append($('<div></div>').css({
                    width: AS.location_point.size,
                    height: AS.location_point.size,
                    position: 'absolute',
                    background: '#000',
                    left: AS.location_point.margin,
                    bottom: AS.location_point.margin,
                }));

                //right-bottom
                this.dom.append($('<div></div>').css({
                    width: AS.location_point.size,
                    height: AS.location_point.size,
                    position: 'absolute',
                    background: '#000',
                    right: AS.location_point.margin,
                    bottom: AS.location_point.margin,
                }));

                //right-top
                this.dom.append($('<div></div>').css({
                    width: AS.location_point.size,
                    height: AS.location_point.size,
                    position: 'absolute',
                    background: '#000',
                    right: AS.location_point.margin,
                    top: AS.location_point.margin,
                }));

                this.locPointRegion = {
                    x: AS.location_point.size + AS.location_point.margin,
                    y: AS.location_point.size + AS.location_point.margin,
                    width: this.width - 2 * (AS.location_point.size + AS.location_point.margin),
                    height: this.height - 2 * (AS.location_point.size + AS.location_point.margin)
                };
                //console.log('locPointRegion', this.locPointRegion);
                //console.groupEnd();
            };

            //导引道 垂直
            surface.initTrackY = function () {
                //console.group('initTrackY');

                this.track_y_list = [];
                var trackBlockWidth = AS.track_y.width;
                var trackBlockHeight = AS.track_y.height;
                var trackBlockSpacing = AS.track_y.spacing;

                var trackHeight = this.contentRegion.height;
                var blockCount = Math.floor(trackHeight / (trackBlockHeight + trackBlockSpacing));
                blockCount += 1;//最后一个track_y加上

                //从上至下
                var blockY = this.contentRegion.y;

                //console.log('track-y-count', blockCount, 'height', trackHeight, 'blockY', blockY);

                var trackYContainer = $('<div class="as-track-y-container"></div>');

                var blockYLeft = this.locPointRegion.x + this.locPointRegion.width - trackBlockWidth - 4;//与定位点相隔的像素,扫切终端需要
                for (var i = 0; i < blockCount; i++) {
                    var css = {
                        width: trackBlockWidth,
                        height: trackBlockHeight,
                        position: 'absolute',
                        background: '#000',
                        left: blockYLeft,
                        top: blockY
                    };

                    this.track_y_list.push({x: blockYLeft, y: blockY});

                    blockY += (trackBlockHeight + trackBlockSpacing);

                    var trackYItem = $('<div class="as-track-y"></div>');
                    trackYItem.attr('id', 'as-track-y-' + i).css(css);
                    if (i == 0) {
                        //第一个
                        trackYItem.addClass('as-track-y-first');
                    } else if (i == blockCount - 1) {
                        //最后一个
                        trackYItem.addClass('as-track-y-last');
                    }

                    trackYContainer.append(trackYItem);
                }

                this.dom.append(trackYContainer);

                if (AS.DEBUG) {
                    var self = this;
                    var trackYIndexLeft = blockYLeft + trackBlockWidth;
                    trackYContainer.find('.as-track-y').each(function (index, el) {
                        $(this).clone().css({
                            position: 'absolute',
                            left: trackYIndexLeft,
                            background: 'transparent',
                            color: '#333',
                            lineHeight: trackBlockHeight + 'px',
                            textAlign: 'left'
                        }).append('<span class="as-small-font">{0}</span>'.format(index)).appendTo(self.dom);

                        $(this).css({
                            cursor: 'pointer'
                        });

                        $(this).click(function () {
                            if (typeof($(this).attr("selected")) == "undefined") {
                                $(this).attr('selected', 'selected');
                            } else {
                                $(this).removeAttr('selected');
                            }
                        }).hover(function () {
                            //hover
                            var row = $(this).attr('id').replace('as-track-y-', '');
                            $(this).addClass('as-grid-row-selected');
                            self.dom.find('.as-track-grid').find('[data-row=' + row + ']').addClass('as-grid-row-selected');
                        }, function () {
                            //out
                            if (typeof($(this).attr("selected")) == "undefined") {
                                $(this).removeClass('as-grid-row-selected');
                                var row = $(this).attr('id').replace('as-track-y-', '');
                                self.dom.find('.as-track-grid').find('[data-row=' + row + ']').removeClass('as-grid-row-selected');
                            }
                        });
                    });
                }
                //console.groupEnd();
            };

            //导引道 水平
            surface.initTrackX = function () {
                //console.group('initTrackX');

                this.track_x_list = [];
                var trackBlockWidth = AS.track_x.width;
                var trackBlockHeight = AS.track_x.height;
                var trackBlockSpacing = AS.track_x.spacing;
                var leftRightSpacing = AS.track_x.spacing;

                var trackWidth = this.contentRegion.width + trackBlockSpacing - leftRightSpacing;

                var blockCount = Math.floor(trackWidth / (trackBlockWidth + trackBlockSpacing));

                //从 左至右
                var blockX = this.contentRegion.x + leftRightSpacing;

                //console.log('track-x-count', blockCount, 'first-position', blockX);

                var blockXTop = this.contentRegion.y + this.contentRegion.height + (AS.track_y.height - AS.track_x.height) / 2;//和最后一条track_y垂直居中对齐
                var trackXContainer = $('<div class="as-track-x-container"></div>');
                var lineBeginX = 0, lineEndX = 0;//实线左端坐标、右端坐标
                trackXContainer.css({
                    position: 'absolute',
                    top: blockXTop,
                });
                for (var i = 0; i < blockCount; i++) {
                    var css = {
                        width: trackBlockWidth,
                        height: trackBlockHeight,
                        position: 'absolute',
                        background: '#000',
                        left: blockX
                    };

                    this.track_x_list.push({x: css.left, y: blockXTop});

                    blockX += (trackBlockWidth + trackBlockSpacing);

                    var trackXItem = $('<div class="as-track-x"></div>');
                    trackXItem.attr('id', 'as-track-x-' + i).css(css);
                    if (i == 0) {
                        //第一个
                        trackXItem.addClass('as-track-x-first');
                        lineBeginX = blockX;
                    } else if (i == blockCount - 1) {
                        //最后一个
                        trackXItem.addClass('as-track-x-last');
                        lineEndX = blockX - (trackBlockWidth + trackBlockSpacing) - trackBlockSpacing;//减掉最后一块
                    } else {
                        //中间的如果是发布模式,隐藏掉
//                        if (!AS.DEBUG) {
//                            trackXItem.css('visibility', 'hidden');
//                        }
                    }

                    trackXContainer.append(trackXItem);
                }

                this.dom.append(trackXContainer);

                //中间实线
//                if (!AS.DEBUG) {
//                    $('<div></div>').css({
//                        position: 'absolute',
//                        height: trackBlockHeight,
//                        top: blockXTop,
//                        left: lineBeginX,
//                        width: lineEndX - lineBeginX,
//                        background: '#000000'
//                    }).appendTo(this.dom);
//                }
                if (AS.DEBUG) {
                    var self = this;
                    var trackXIndexTop = blockXTop + trackBlockHeight;
                    trackXContainer.find('.as-track-x').each(function (index, el) {
                        $(this).clone().css({
                            position: 'absolute',
                            top: trackXIndexTop,
                            background: '#fff',
                            color: '#333',
                            textAlign: 'center'
                        }).append('<span class="as-small-font">{0}</span>'.format(index)).appendTo(self.dom);
                        $(this).css({
                            cursor: 'pointer'
                        });
                        $(this).click(function () {
                            if (typeof($(this).attr("selected")) == "undefined") {
                                $(this).attr('selected', 'selected');
                            } else {
                                $(this).removeAttr('selected');
                            }
                        }).hover(function () {
                            //hover
                            $(this).addClass('as-grid-col-selected');
                            var col = $(this).attr('id').replace('as-track-x-', '');
                            self.dom.find('.as-track-grid').find('[data-col=' + col + ']').addClass('as-grid-col-selected');
                        }, function () {
                            //out
                            if (typeof($(this).attr("selected")) == "undefined") {
                                $(this).removeClass('as-grid-col-selected');
                                var col = $(this).attr('id').replace('as-track-x-', '');
                                self.dom.find('.as-track-grid').find('[data-col=' + col + ']').removeClass('as-grid-col-selected');
                            }
                        });
                    });
                }
                //console.groupEnd();
            };

            surface.buildBarcode = function (code) {
                //console.log('buildBarcode---start', code);
                var top = this.dom.find('.as-track-y-first').position().top;
                var right = this.width - (this.dom.find('.as-track-x-last').position().left + AS.track_x.width);

                var barcodeId = 'bc-' + code;

                var barcodeDom = $('<svg id="' + barcodeId + '"></svg>');
                barcodeDom.css({
                    position: 'absolute',
                    top: top,
                    right: right
                });

                this.dom.append(barcodeDom);

                JsBarcode('#' + barcodeId, code, {
                    format: "code128",
                    width: 2,
                    height: AS.track_y.height,
                    margin: 0,
                    displayValue: false
                });

                //每张纸的右上角
                var cols = Math.ceil(barcodeDom.outerWidth(true) / (AS.track_x.width + AS.track_x.spacing));
                var beginRegionTrackY = 0;
                var endRegionTrackY = 0;
                var endRegionTrackX = this.track_x_list.length - 1;
                var beginRegionTrackX = endRegionTrackX - cols;
                AS.barcode.location = [beginRegionTrackY, endRegionTrackY, beginRegionTrackX, endRegionTrackX];//[开始行,结束行,开始列,结束列]
                //console.log('buildBarcode---end', code);
            };

            //页面分隔线
            surface.initPageSplitter = function () {
                $('<div></div>').css({
                    width: 0,
                    height: '100%',
                    borderLeft: 'dashed 1px #d5d5d5',
                    position: 'absolute',
                    left: this.contentRegion.x + Math.floor(this.contentRegion.width / this.cols),
                    top: '0',
                    bottom: '0'
                }).appendTo(this.dom);
            };

            //辅助网络
            surface.buildHelpGrid = function () {
                //console.group('buildHelpGrid');
                var gridBlockSize = {
                    width: AS.track_x.width,
                    height: AS.track_y.height
                };
                var self = this;

                var trackGridDom = $('<div class="as-track-grid"></div>');
                self.track_y_list.forEach(function (trackY, indexY, array) {
                    if (indexY + 1 == array.length) {
                        return;//最后一行不画
                    }
                    self.track_x_list.forEach(function (trackX, indexX, array) {
                        $('<div id="{id}" data-row={row} data-col={col}></div>'.format({
                            id: 'as-grid-' + indexY + '-' + indexX,
                            row: indexY,
                            col: indexX
                        })).css({
                            position: 'absolute',
                            width: gridBlockSize.width,
                            height: gridBlockSize.height,
                            background: (indexY % 2 ? '#d3d3d3' : '#c5c5c5'),
                            left: trackX.x,
                            top: trackY.y
                        }).appendTo(trackGridDom);
                    });
                });
                trackGridDom.appendTo(this.dom);
                //console.groupEnd();
            };

            return surface;
        }
    };

    var Page = {
        store: [],//保存创建的Page对象
        usedHeight: 0,//已使用高度,从第一页到最后一页
        totalHeight: 0,//所有页面高度
        incUsedHeight: function (height) {//增加高度
            this.usedHeight += height;
            return this.usedHeight;
        },
        height: function () {
            return this.store[0].region.height;
        },
        fetch: function (height/*测量值,可正可负*/, isAlignTrackY/*是否对齐track_y*/, usedHeight/*已使用高度,默认为当前实际使用高度*/) {//提取当前页面,判断加上height之后应该在哪个页面
            if (arguments.length == 1) {
                height = arguments[0];
                isAlignTrackY = true;
                usedHeight = this.usedHeight;
            } else if (arguments.length == 2) {
                height = arguments[0];
                isAlignTrackY = arguments[1];
                usedHeight = this.usedHeight;
            } else if (arguments.length >= 3) {
                height = arguments[0];
                isAlignTrackY = arguments[1];
                usedHeight = arguments[2];
            } else {
                height = 0;
                isAlignTrackY = true;
                usedHeight = this.usedHeight;
            }

            var pageHeight = this.store[0].region.height;

            var usedPageIndex = Math.floor(usedHeight / pageHeight);//当前已到第几页,页码从0开始,0代表第1页,1代表第2页

            var usedPageHeight = usedHeight % pageHeight;//在当页中已使用的高度值

            //纠正索引,避免越界
            if (usedPageIndex < 0) {
                console.error('fetch page', '新增加的高度值超过页面可表达范围【小于最小页面值】', {
                    usedPageIndex: usedPageIndex,
                    usedHeight: usedHeight,
                    usedPageHeight: usedPageHeight
                });
                //throw '新增加的高度值超过页面可表达范围【小于最小页面值】' + usedPageIndex;
                usedPageIndex = 0;
            } else if (usedPageIndex >= this.store.length) {
                //TODO 后续可动态创建页面
                console.error('fetch page', '新增加的高度值超过页面可表达范围【大于最大页面值】', {
                    usedPageIndex: usedPageIndex,
                    usedHeight: usedHeight,
                    usedPageHeight: usedPageHeight
                });
                //throw '新增加的高度值超过页面可表达范围【大于最大页面值】' + usedPageIndex;
                usedPageIndex = this.store.length - 1;
            }
            var pageTop = 0;
            var offset = 0;//调整对齐track_y前后,page_top偏移量
            var track_y = -1;//第几个track_y,从0开始,-1代表不知道

            if (isAlignTrackY) {
                //找离给定坐标下方最近的一条track_y
                var oldPageTop = usedPageHeight;
                var pageTrackY = this.store[usedPageIndex].nearestTrackY(oldPageTop);
                var targetPageHeight = pageTrackY.y + height;
                if (targetPageHeight > pageHeight) {
                    //TODO 本页放不下,要跨几页,分页排布
                    usedPageIndex += Math.floor(targetPageHeight / pageHeight);
                    //纠正索引,避免越界
                    if (usedPageIndex < 0) {
                        throw '新增加的高度值超过页面可表达范围-【小于最小页面值】' + usedPageIndex;
                    } else if (usedPageIndex >= this.store.length) {
                        //TODO 后续可动态创建页面
                        throw '新增加的高度值超过页面可表达范围-【大于最大页面值】' + usedPageIndex;
                    }
                    oldPageTop = targetPageHeight % pageHeight;
                    pageTrackY = this.store[usedPageIndex].nearestTrackY(oldPageTop);
                }

                track_y = pageTrackY.index;
                pageTop = pageTrackY.y;
                offset = pageTrackY.y - oldPageTop;
            } else {
                //不需要对齐track_y,只计算出位置即可
                var newPageHeight = usedPageHeight + height;
                var incPageCount = Math.floor(newPageHeight / pageHeight);//增加height后,增减了几页
                if (incPageCount > 0) {
                    //增加页
                    pageTop = Math.abs(newPageHeight % pageHeight);//需要增加到的高度
                } else if (incPageCount < 0) {
                    //减少页
                    pageTop = Math.abs(newPageHeight % pageHeight);
                } else {
                    //没增也没减,还在当页
                    pageTop = usedPageHeight;
                }

                usedPageIndex += incPageCount;
                offset = 0;
                track_y = -1;
                console.log('fetch', usedHeight, usedPageHeight, usedPageIndex, pageTop);
            }
            //遇到barcode需要调整
            //跳过barcode所占区域
            if (pageTop < AS.track_y.height && usedPageIndex % 2 != 0) {
                //barcode所在页面,从第3个track_y开始使用
                track_y = 2;
                pageTrackY = this.store[usedPageIndex].surface.track_y_list[track_y];//
                pageTrackY = this.store[usedPageIndex].projectSurfaceToPage(pageTrackY.x, pageTrackY.y);//把surface转换为page坐标
                offset = pageTrackY.y - pageTop;
                pageTop = pageTrackY.y;
            }

            return {
                page: this.store[usedPageIndex],
                index: usedPageIndex,
                top: pageTop,
                offset: offset,
                track_y: track_y
            };
        },
        createNew: function (pageDom, surface) {
            var page = {};

            page.num = parseInt(pageDom.attr('data-page'));//第几页,从0开始
            page.total = parseInt(pageDom.attr('data-total'));//共几页
            page.surface = surface;
            page.region = {x: 0, y: 0, width: 0, height: 0};
            page.asDom = surface.asDom;
            page.dom = pageDom;
            page.init = function () {
                //console.group('page-init start');

                var contentRegion = this.surface.contentRegion;
                this.region.width = Math.floor(contentRegion.width / this.surface.cols) - this.surface.colSpacing;
                this.region.height = contentRegion.height;

                var left = (this.num % this.surface.cols) * this.region.width + contentRegion.x + (this.num % this.surface.cols) * 2 * this.surface.colSpacing;

                this.region.x = left;
                this.region.y = contentRegion.y;

                this.dom.css({
                    width: this.region.width,
                    height: this.region.height,
                    position: 'absolute',
                    left: this.region.x,
                    top: this.region.y,
                    overflowY: 'hidden'
                });

                Page.totalHeight += this.region.height;

                //console.log('page size', this.region);

                this.initFooter();

                //console.groupEnd();
            };
            //
            page.initFooter = function () {
                var footer = '<div><span class="as-small-font as-main-color">第{page}页 共{total}页</span></div>';
                $(footer.format({page: page.num + 1, total: page.total})).css({
                    position: 'absolute',
                    top: this.surface.locPointRegion.y + this.surface.locPointRegion.height - AS.footer.height,
                    left: (this.num % this.surface.cols) * this.region.width + this.surface.contentRegion.x,
                    width: this.region.width,
                    height: AS.footer.height,
                    fontSize: '1.0rem',
                    display: 'block',
                    textAlign: 'center'
                }).appendTo(this.surface.dom);
            };

            //获取距离指定坐标最近的track_x的索引
            page.nearestTrackX = function (pageX/*x坐标*/, isRight/*往右还是往左*/) {
                /**wkhtmltopdf 不支持默认参数**/
                if (arguments.length == 1) {
                    pageX = arguments[0];
                    isRight = true;
                } else if (arguments.length >= 2) {
                    pageX = arguments[0];
                    isRight = arguments[1];
                } else {
                    pageX = 0;
                    isRight = true;
                }
                var leftX = this.projectPageToSurface(pageX, 0).x;
                var blockIndex = leftX / (AS.track_x.width + AS.track_x.spacing);
                var blockIndexCeil = Math.ceil(blockIndex);
                var blockIndexFloor = Math.floor(blockIndex);
                if (blockIndexCeil == blockIndexFloor) {
                    if (isRight == false) {
                        blockIndex = blockIndexFloor - 1;
                    } else {
                        blockIndex = blockIndexCeil;
                    }
                } else {
                    if (isRight == false) {
                        blockIndex = blockIndexFloor;
                    } else {
                        blockIndex = blockIndexCeil;
                    }
                }
                //索引从0开始
                blockIndex -= 1;

                //判断是否越界
                if (blockIndex < 0) {
                    blockIndex = 0;
                } else if (blockIndex >= this.surface.track_x_list.length) {
                    blockIndex = this.surface.track_x_list.length - 1;
                }
                //console.log('nearestTrackX', pageX, blockIndex + '-' + blockIndexCeil + '-' + blockIndexFloor);
                var trackXBlock = this.surface.track_x_list[blockIndex];
                var newXY = this.projectSurfaceToPage(trackXBlock.x, trackXBlock.y);
                return {index: blockIndex, x: newXY.x, y: newXY.y, offset: newXY.y - pageX};
            };

            //获取距离指定坐标最近的track_y的索引
            page.nearestTrackY = function (pageY/*y坐标*/, isDown/*往上还是往下*/) {
                /**wkhtmltopdf 不支持默认参数**/
                if (arguments.length == 1) {
                    pageY = arguments[0];
                    isDown = true;
                } else if (arguments.length >= 2) {
                    pageY = arguments[0];
                    isDown = arguments[1];
                } else {
                    pageY = 0;
                    isDown = true;
                }

                var pageXY = this.projectPageToSurface(0, pageY);
                var topY = pageXY.y;//
                var blockIndex = -1;//
                for (var i = 0; i < this.surface.track_y_list.length; i++) {
                    if (topY <= this.surface.track_y_list[i].y) {
                        if (isDown) {
                            blockIndex = i;
                        } else {
                            blockIndex = i - 1;
                        }
                        break;
                    }
                }

                //判断是否越界
                if (blockIndex < 0) {
                    blockIndex = 0;
                }

                var trackYBlock = this.surface.track_y_list[blockIndex];
                var newXY = this.projectSurfaceToPage(trackYBlock.x, trackYBlock.y);
                return {index: blockIndex, x: newXY.x, y: newXY.y, offset: newXY.y - pageY};
            };

            //把surface中的坐标转换为page中的坐标
            page.projectSurfaceToPage = function (surfaceX, surfaceY) {
                var pageX = surfaceX - this.region.x;
                var pageY = surfaceY - this.region.y;
                return {x: pageX, y: pageY};
            };

            //把page中的坐标转换为surface中的坐标
            page.projectPageToSurface = function (pageX, pageY) {
                var surfaceX = pageX + this.region.x;
                var surfaceY = pageY + this.region.y;
                return {x: surfaceX, y: surfaceY};
            };
            return page;
        }
    };

    var ASBuilder = {
        asDom: null, /*答题卡容器对象*/
        init: function (as, paper) {
            AS = as;
            Paper = paper;
            //console.log('init-start>>>>');
            //TODO 清空内容
            // 初始化AnswerSheet Dom对象
            this.asDom = $('#answer-sheet-container');//转JQuery对象
            this.asDom.html('');

            //TODO debug
            AS.DEBUG = false;

            //构建答题卡对象
            //this.buildAS();

            var surfaceCount = 2;//有几页纸
            for (var i = 0; i < surfaceCount; i++) {
                //一张纸,正反面
                var surfaceDom = $('<div class="as-surface"></div>');
                this.asDom.append(surfaceDom);
                var surface = Surface.createNew(surfaceDom, this.asDom);
                if (i > 0) {
                    surfaceDom.addClass('page-break');
                }
                surface.init();
                surface.buildBarcode(Paper.id + '-' + (i + 1));

                for (var j = 0; j < AS.cols; j++) {
                    var pageDom = $('<div class="as-page" data-page="{page}" data-total="{total}"></div>'.format({
                        page: i * surfaceCount + j,
                        total: surfaceCount * AS.cols
                    }));
                    surfaceDom.append(pageDom);
                    var page = Page.createNew(pageDom, surface);
                    page.init();
                    //
                    Page.store.push(page);
                }
            }

            //TODO 构建内容区域
            ASBuilder.buildHeader(Page.store[0]);//第一页
            ASBuilder.buildQuestionRegion();

            //console.log('>>>>init-end');
        },
        buildHeader: function (page) {

            // title
            var titleDom = $('<div class="as-title-container"><div style="display:table-cell;vertical-align: middle;"><span>{title}</span></div></div>'.format({
                title: AS.title
            }));

            titleDom.css({
                wordBreak: 'break-all',
                textAlign: 'center',
                width: '100%',
                height: 70,
                lineHeight: '1.4rem',
                display: 'table',
                paddingLeft: '10px',
                paddingRight: '10px',
                overflow: 'hidden',
            });
            page.dom.append(titleDom);

            var titleHeight = titleDom.outerHeight(true);

            // card
            var cardContainer = $('<div class="as-card-container"></div>');
            var cardNumLeftOffset = AS.track_x.spacing / 2;//
            var cardContainerWidth = (AS.track_x.width + AS.track_x.spacing) * AS.card.length;
            var cardTitleRowHeight = AS.track_y.height * 2;
            var cardNumWriteRowHeight = AS.track_y.height + AS.track_y.spacing * 2;//手写栏所占高度

            var cardContainerTop = page.nearestTrackY(titleHeight).y;
            var cardContainerLeft = page.nearestTrackX(page.region.width, false).x - cardContainerWidth - cardNumLeftOffset;

            cardContainer.css({
                width: cardContainerWidth,
                height: cardTitleRowHeight + cardNumWriteRowHeight + (AS.track_y.height + AS.track_y.spacing) * 10 + AS.track_y.spacing,
                border: 'solid 1px #c895b4',
                boxSizing: 'border-box',
                position: 'absolute',
                overflow: 'hidden',
                left: cardContainerLeft,
                top: cardContainerTop
            });

            $('<div><span class="as-small-font">准考证号</span></div>').css({
                width: cardContainerWidth,
                height: cardTitleRowHeight,
                textAlign: 'center',
                lineHeight: cardTitleRowHeight + 'px',
                boxSizing: 'border-box'
            }).appendTo(cardContainer);

            var trackYItem = page.nearestTrackY(cardContainerTop + cardTitleRowHeight + cardNumWriteRowHeight);
            var trackXItem = page.nearestTrackX(cardContainerLeft - 5);
            //TODO 设置card location
            var beginRegionTrackY = trackYItem.index;
            var endRegionTrackY = trackYItem.index + 10 - 1;//0...9数字共10个
            var beginRegionTrackX = trackXItem.index;
            var endRegionTrackX = trackXItem.index + AS.card.length - 1;
            AS.card.location = [beginRegionTrackY, endRegionTrackY, beginRegionTrackX, endRegionTrackX];//[开始行,结束行,开始列,结束列]
            AS.card.items = [];


            var cardNumContainer = $('<div></div>');

            for (var col = 0; col < AS.card.length; col++) {
                //背景
                if (!AS.DEBUG) {
                    $('<div class="as-card-num-bg"></div>').css({
                        position: 'absolute',
                        width: AS.track_x.width + AS.track_x.spacing,
                        height: (AS.track_y.height + AS.track_y.spacing) * 10 + cardNumWriteRowHeight + AS.track_y.spacing - 1,
                        textAlign: 'center',
                        boxSizing: 'box-border',
                        background: col % 2 == 0 ? '#f8e8f2' : '#fff',
                        left: col * (AS.track_x.width + AS.track_x.spacing) - 1,
                        top: cardTitleRowHeight + cardNumWriteRowHeight + 1
                    }).appendTo(cardNumContainer);
                }
                //添加手写空间
                $('<div class="as-card-num-write"></div>').css({
                    position: 'absolute',
                    width: AS.track_x.width + AS.track_x.spacing,
                    height: cardNumWriteRowHeight,
                    textAlign: 'center',
                    left: col * (AS.track_x.width + AS.track_x.spacing),
                    top: cardTitleRowHeight,
                    letterSpacing: '2px',
                }).appendTo(cardNumContainer);
            }
            /*先列后行*/
            var cardNumFirstTop = cardTitleRowHeight + cardNumWriteRowHeight + AS.track_y.spacing - 1;//TODO 字体有向下便宜
            for (var col = 0; col < AS.card.length; col++) {
                //线框
                $('<div></div>').css({
                    position: 'absolute',
                    width: 1,
                    height: (AS.track_y.height + AS.track_y.spacing) * 10 + cardNumWriteRowHeight + AS.track_y.spacing - 1,
                    textAlign: 'center',
                    background: '#c895b4',
                    boxSizing: 'box-border',
                    left: (col + 1) * (AS.track_x.width + AS.track_x.spacing) - 1,
                    top: cardTitleRowHeight + 1
                }).appendTo(cardNumContainer);

                var cardNumColWrapper = $('<div class="as-card-num"></div>');
                var colLocationArray = [];
                for (var row = 0; row < 10; row++) {
                    //[开始行,结束行,开始列,结束列]
                    var beginRow = beginRegionTrackY + row;
                    var endRow = beginRow;
                    var beginCol = beginRegionTrackX + col;
                    var endCol = beginCol;

                    cardNumColWrapper.css({
                        position: 'absolute',
                        width: AS.track_x.width,
                        paddingLeft: AS.track_x.spacing / 2,
                        paddingRight: AS.track_x.spacing / 2,
                        textAlign: 'center',
                        boxSizing: 'box-border',
                        left: col * (AS.track_x.width + AS.track_x.spacing) - cardNumLeftOffset,
                        top: cardNumFirstTop
                    });
                    $('<div><span class="as-small-font as-bracket as-main-color">{0}</span></div>'.format(row)).css({
                        position: 'absolute',
                        width: '100%',
                        textAlign: 'center',
                        height: AS.track_y.height,
                        lineHeight: (AS.track_y.height) + 'px',
                        letterSpacing: '2px',
                        top: row * (AS.track_y.height + AS.track_y.spacing)
                    }).appendTo(cardNumColWrapper);

                    colLocationArray.push([beginRow, endRow, beginCol, endCol]);
                }

                cardNumColWrapper.appendTo(cardNumContainer);

                //处理数据格式,记录location
                AS.card.items.push(colLocationArray);
            }

            cardContainer.append(cardNumContainer);

            page.dom.append(cardContainer);

            var cardWidth = (page.region.x + page.region.width) - cardContainer.position().left;

            var siTableLeftOffset = AS.track_x.width + AS.track_x.spacing;

            var usableWidth = page.region.width - cardWidth - page.surface.colSpacing - siTableLeftOffset;
            // student info
            var siTable = $('<table class="as-student">\
                    <tr>\
                    <td class="as-td-label"><span class="as-small-font"><div style="line-height: 20px;">姓</div><div style="line-height: 20px;">名</div></span></td>\
                    <td></td>\
                    </tr>\
                    <tr>\
                    <td class="as-td-label"><span class="as-small-font"><div style="line-height: 20px;">班</div><div style="line-height: 20px;">级</div></span></td>\
                    <td></td>\
                    </tr>\
                    </table>');

            siTable.css({
                width: usableWidth,
                position: 'absolute',
                left: siTableLeftOffset,
                top: cardContainer.position().top
            });
            page.dom.append(siTable);

            var siTableHeight = siTable.outerHeight(true);
            //填涂要求
            var notesWrapperTop = siTable.position().top + siTableHeight;
            var notesWrapperTrackY = page.nearestTrackY(notesWrapperTop);
            notesWrapperTop = notesWrapperTrackY.y;
            var notesWrapper = $('<div></div>');
            notesWrapper.css({
                width: usableWidth,
                position: 'absolute',
                left: siTableLeftOffset,
                top: notesWrapperTop
            });

            var labelWidth = 40;
            var missExamTable = $('<table class="as-student" style="width:100%;border-bottom:none;">\
            <tr>\
            <td style="width:{labelWidth}px;height:{height}px;border-bottom:none;">\
                    <span class="as-small-font">缺 考</span>\
            </td>\
            <td style="border-bottom:none;">\
                    <div class="as-miss-exam-flag"></div>\
                    </td>\
                    </tr>\
                    </table>'.format({
                labelWidth: labelWidth,
                height: (AS.track_y.height + AS.track_y.spacing) * 2
            }));
            var examFlagDom = missExamTable.find('.as-miss-exam-flag');
            var examWrapperWidth = Math.floor((usableWidth - labelWidth) / 2);
            var examTrackX = page.nearestTrackX(examWrapperWidth + labelWidth);
            //TODO 调整缺考标记位置
            examFlagDom.css({
                position: 'absolute',
                width: AS.track_x.width,
                height: AS.track_y.height,
                border: 'solid 1px #c895b4',
                boxSizing: 'border-box',
                top: AS.track_y.height + AS.track_y.spacing,
                left: examTrackX.x - siTableLeftOffset
            });
            notesWrapper.append(missExamTable);

            $('<table class="as-student" style="width:100%;">\
                    <tr>\
                    <td rowspan="2" class="as-td-label">\
                    <span class="as-small-font" style="width:10px;">填涂要求</span>\
                    </td>\
                    <td rowspan="2" style="text-align: left;vertical-align: middle;">\
                    <span class="as-small-font" style="height:70px;padding:0 5px;overflow: hidden;">\
                    <div style="padding-top:10px;">1.必须用2B铅笔填涂。</div>\
            <div style="padding:5px 0;">2.此卡不准弄脏、弄皱或弄破，严禁折叠。</div>\
            <div style="padding-bottom:5px;">3.修改时用橡皮擦干净。</div></span>\
            </td>\
            <td style="width">\
            <span class="as-small-font" style="padding-top:4px;">正确填涂</span>\
                    <div style="width:21px;height:7px;background:#333;margin:6px auto;"></div>\
                    </td>\
                    </tr>\
                    <tr>\
                    <td style="width:60px;">\
                    <span class="as-small-font" style="padding-top:3px;">错误填涂</span>\
                    <img src="/dist/img/ic_as_demo.png" style="padding:3px 5px;"/>\
                    </td>\
                    </tr>\
                    </table>').appendTo(notesWrapper);

            AS.miss_exam.location = [notesWrapperTrackY.index + 1, notesWrapperTrackY.index + 1, examTrackX.index, examTrackX.index];//[开始行,结束行,开始列,结束列]
            page.dom.append(notesWrapper);

            //计算卷首占居的高度
            var notesWrapperHeight = notesWrapper.position().top + notesWrapper.outerHeight(true);
            var cardContainerHeight = cardContainer.position().top + cardContainer.outerHeight(true);
            Page.usedHeight += (notesWrapperHeight > cardContainerHeight ? notesWrapperHeight : cardContainerHeight);
        },
        //构建答题区域
        buildQuestionRegion: function () {
            //console.group('buildQuestionRegion -- start');
            var self = this;
            AS.items.forEach(function (questionType, index, array) {
                var category = questionType.category;
                if (category.category == 1) {//客观题
                    self.buildQT1(questionType);
                } else if (category.category == 2) {//主观题
                    self.buildQT2(questionType);
                } else {
                    //无效题型
                    console.error('question-type', 'invalid question-type', questionType);
                }
            });

            //console.groupEnd();
        },
        buildQT1: function (questionType) {
            //console.group('buildQT1 start', questionType);
            //客观题
            //category_option_category
            var self = this;
            var optionList = [];
            var category = questionType.category;
            switch (category.option_category) {
                case 1://单选
                case 2://多选
                    var maxOptionCount = 0;
                    questionType.questions.forEach(function (question, index, array) {
                        var optionCount = question.options.length;
                        if (optionCount > maxOptionCount) {
                            maxOptionCount = optionCount;
                        }
                    });
                    optionList = self.generateAlphabet(maxOptionCount);
                    break;
                case 3://判断
                    optionList = ['T', 'F'];
                    break;
                default://无效
                    return '';
            }

            //添加题型title & desc
            this.buildQTypeTitle(questionType);

//            Page.incUsedHeight(AS.track_y.spacing);

            var pageWrapper = Page.fetch();

            var page = pageWrapper.page;

            //答题卡填涂项
            var questionCount = questionType.questions.length;
            //track_x item宽度
            var trackXItemWidth = AS.track_x.width + AS.track_x.spacing;
            //每个列块(栏)的宽度
            var colBlockWidth = (optionList.length + 1) * (AS.track_x.width + AS.track_x.spacing);

            //两栏之间的间隔
            var colBlockSpacing = trackXItemWidth;

            //每行共可分多少栏(需要考虑对齐track_x占用的空间)
            var colBlockPerRow = this.generateColsInRow(colBlockWidth, Math.floor(page.region.width / colBlockWidth), page.region.width, colBlockSpacing).cols;

            var trackYItemHeight = AS.track_y.height + AS.track_y.spacing;
            var rowPerBlock = 5;//每几行形成一个行块
            var rowCount = Math.ceil(questionCount / colBlockPerRow);//共有多少行
            //console.log('colBlockWidth', colBlockWidth, 'colBlockPerRow', colBlockPerRow, 'rowCount', rowCount, 'optionList.length', optionList.length);

            var colCss = {
                position: 'absolute',
                width: AS.track_x.width,
                height: AS.track_y.height,
                lineHeight: AS.track_y.height + 'px',
                textAlign: 'center'
            };

            //遍历试题
            var prevPageIndex = -1;//页面索引
            var isBeginRegion = false;//是否开始一个区域框
            var regionTrack = {start_x: 0, end_x: 0, start_y: 0, end_y: 0};
            var regionRect = {x: 0, y: 0, width: 0, height: 0};
            questionType.coordinate = [];
            for (var row = 0; row < rowCount; row++) {
                //TODO 判断应该在哪页显示
                var tmpPageWrapper = Page.fetch(trackYItemHeight);
                if (AS.DEBUG) {
                    //console.log('--build question--', prevPageIndex, tmpPageWrapper.index, Page.usedHeight, tmpPageWrapper.page.region.height, tmpPageWrapper.top, tmpPageWrapper.offset);
                }

                var questionContainer = $('<div></div>');
                var trackXItem = tmpPageWrapper.page.nearestTrackX(0);
                var left = trackXItem.x;
                questionContainer.css({
                    position: 'absolute',
                    top: tmpPageWrapper.top,
                    left: left
                });

                var trackYStart = tmpPageWrapper.track_y;
                var trackXStart = trackXItem.index;

                //栏
                var leftOffset = 0;
                var trackXOffset = 0;

                for (var col = 0; col < colBlockPerRow; col++) {
                    //超过试题数量隐藏
                    var questionIndex = row * colBlockPerRow + col;
                    if (questionIndex >= questionCount) {
                        //没有题了,直接返回
                        break;
                    }
                    //TODO 处理location
                    var question = questionType.questions[questionIndex];
                    //题号
                    colCss.letterSpacing = 'auto';
                    colCss.left = col * colBlockWidth + leftOffset;
                    $('<div><span class="as-small-font">{0}</span></div>'.format(row * colBlockPerRow + col + 1)).css(colCss).appendTo(questionContainer);
                    //选项
                    colCss.letterSpacing = '2px';
                    for (var opt = 0; opt < optionList.length; opt++) {
                        colCss.left += trackXItemWidth;
                        trackXOffset++;
                        $('<div><span class="as-small-font as-bracket as-main-color">{0}</span></div>'.format(optionList[opt])).css(colCss).appendTo(questionContainer);
                        var questionOption = question.options[opt];
                        if (questionOption) {
                            questionOption.page = tmpPageWrapper.index;
                            questionOption.location = [trackYStart, trackYStart, trackXStart + trackXOffset, trackXStart + trackXOffset];
                        }
                    }
                    leftOffset += colBlockSpacing;
                    trackXOffset += 2;//栏间隔 + 题号所在列
                }

                if (prevPageIndex != tmpPageWrapper.index) {

                    if (prevPageIndex != -1) {
                        //有分页

                        //处理题型location区域
                        //S1 要换页了,结束本页的数据处理
                        var prevPage = Page.store[prevPageIndex];
                        regionTrack.end_y = prevPage.surface.track_y_list.length - 2;//最后一块可用track_y

                        var prevTrackXItem = prevPage.nearestTrackX(tmpPageWrapper.page.region.width);
                        regionTrack.end_x = prevTrackXItem.index;

                        questionType.coordinate.push({
                            page: prevPageIndex,
                            location: [regionTrack.start_y, regionTrack.end_y, regionTrack.start_x, regionTrack.end_x]
                        });

                        isBeginRegion = false;

                        //画框
                        var endTrackY = prevPage.surface.track_y_list[regionTrack.end_y];

                        //与最后一块track_x比,哪个小用哪个
                        regionRect.width = tmpPageWrapper.page.region.width > prevTrackXItem.x + AS.track_x.width + AS.track_x.spacing ? prevTrackXItem.x + AS.track_x.width + AS.track_x.spacing : tmpPageWrapper.page.region.width;
                        regionRect.height = prevPage.projectSurfaceToPage(endTrackY.x, endTrackY.y).y - regionRect.y + AS.track_y.height;
                        //console.log('----框线----', regionRect, endTrackY, prevPage.projectSurfaceToPage(endTrackY.x, endTrackY.y));
                        $('<div></div>').css({
                            position: 'absolute',
                            borderTop: 'solid 1px #c895b4',
                            borderLeft: 'solid 1px #c895b4',
                            borderRight: 'solid 1px #c895b4',
                            top: regionRect.y,
                            left: 0,
                            width: regionRect.width,
                            height: regionRect.height,
                            boxSizing: 'border-box'
                        }).appendTo(prevPage.dom);
                    }
                    //TODO 分页
                    prevPageIndex = tmpPageWrapper.index;
                }

                if (!isBeginRegion) {
                    //开始一个区域框,初始化开始位置
                    regionTrack.start_y = trackYStart;
                    regionTrack.start_x = trackXStart;

                    regionRect.x = trackXItem.x;
                    regionRect.y = tmpPageWrapper.top - AS.track_y.spacing;

                    isBeginRegion = true;
                }

                //S2
                if ((row == rowCount - 1) && isBeginRegion) {
                    //该题型试题已 结束了
                    isBeginRegion = false;

                    var trackXEnd = tmpPageWrapper.page.nearestTrackX(tmpPageWrapper.page.region.width);
                    regionTrack.end_y = trackYStart;
                    regionTrack.end_x = trackXEnd.index;
                    questionType.coordinate.push({
                        page: tmpPageWrapper.index,
                        location: [regionTrack.start_y, regionTrack.end_y, regionTrack.start_x, regionTrack.end_x]
                    });

                    //画框
                    var endTrackY = tmpPageWrapper.page.surface.track_y_list[regionTrack.end_y];

                    //与最后一块track_x比,哪个小用哪个
                    regionRect.width = tmpPageWrapper.page.region.width > trackXEnd.x + AS.track_x.width + AS.track_x.spacing ? trackXEnd.x + AS.track_x.width + AS.track_x.spacing : tmpPageWrapper.page.region.width;

                    regionRect.height = tmpPageWrapper.page.projectSurfaceToPage(endTrackY.x, endTrackY.y).y - regionRect.y + AS.track_y.height;
                    $('<div></div>').css({
                        position: 'absolute',
                        border: 'solid 1px #c895b4',
                        top: regionRect.y,
                        left: 0,
                        width: regionRect.width,
                        height: regionRect.height + AS.track_y.spacing * 2,
                        boxSizing: 'border-box'
                    }).appendTo(tmpPageWrapper.page.dom);
                }

                questionContainer.appendTo(tmpPageWrapper.page.dom);

                Page.usedHeight += trackYItemHeight + tmpPageWrapper.offset;//TODO

                //判断是否要划分行块
                if (row > 0 && row % rowPerBlock == 0) {
                    //每5行一个区块
                    Page.usedHeight += trackYItemHeight;
                }
            }

//            Page.incUsedHeight(AS.track_y.spacing);
            //console.groupEnd();
        },
        buildQT2: function (questionType) {
            //console.group('buildQT2', questionType);
            //主观题
            var self = this;

            //添加题型title & desc
            var titleDom = this.buildQTypeTitle(questionType);

            //答题卡填涂项
            questionType.questions.forEach(function (question, index, array) {
                //console.group('question', index);
                question.coordinate.forEach(function (coordinate, index, array) {
                    self.buildAnswerRegion(question, coordinate);
                });
                //console.groupEnd();
            });

            //console.groupEnd();
        },
        buildQTypeTitle: function (questionType) {
            var title = questionType.title;
            var titleRegion = questionType.title_region;
            //添加题型title & desc
            var titleDomHeight = titleRegion.used_height;
            var titleDom = $('<div><div style="position:absolute;bottom:5px;width:100%;">{0}</div></div>'.format(title));

            titleDom.css({
                position: 'absolute',
                height: titleRegion.rect[3],
                width: titleRegion.rect[2],
                left: titleRegion.rect[0],
                top: titleRegion.rect[1],
            });

            Page.store[titleRegion.page].dom.append(titleDom);

            Page.usedHeight += titleDomHeight;
            return titleDom;
        },
        generateAlphabet: function ($length) {
            /**wkhtmltopdf 不支持默认参数**/
            if (arguments.length == 1) {
                $length = arguments[0];
            } else {
                $length = 26;
            }
            var str = [];
            //A=65
            var start = 65;
            var end = start + $length;
            for (var i = start; i < end; i++) {
                str.push(String.fromCharCode(i));
            }
            return str;
        },
        generateColsInRow: function (colBlockWidth/*栏宽*/, colBlockPerRow/*每行多少栏*/, rowFreeWidth/*行可用宽度*/, colBlockSpacing/*两栏之间的间隔*/) {
            //剩余宽度,判断剩余宽度是否有足够的空间可用
            var freeWidth = rowFreeWidth - colBlockPerRow * colBlockWidth;
            var spacingTotalWidth = (colBlockPerRow - 1) * colBlockSpacing;
            if (freeWidth >= spacingTotalWidth) {
                //空间可划分
                return {cols: colBlockPerRow};
            } else {
                //不可以使用,重新计算空间
                return this.generateColsInRow(colBlockWidth, colBlockPerRow - 1, rowFreeWidth, colBlockSpacing);
            }
        },
        buildAnswerRegion: function (question, coordinate) {
            //作答区域
            var answerContainer = $('<div class="as-answer-container"></div>');

            var answerDom = $('<div class="as-answer"></div>');

//            answerDom.css({
//                padding: 10
//            });
            answerContainer.append(answerDom);

            //TODO 判断应该在哪页显示

            answerContainer.css({
                position: 'absolute',
                left: coordinate.rect[0],
                top: coordinate.rect[1],
                width: coordinate.rect[2],
                height: coordinate.rect[3],
                overflow: 'hidden',
                border: 'solid 1px #c895b4',
                boxSizing: 'border-box',
            });

            answerDom.append(coordinate.attachment);

//            Page.usedHeight += coordinate.height;

            Page.store[coordinate.page].dom.append(answerContainer);
        },
    };

</script>
<script>
    $(function () {
        var paperId = '{{$paper->id}}';
        var examPaperId = '{{$ep_id}}';
        $.ajax({
            type: 'GET',
            url: '/lan/as/get',
            data: {paper_id: paperId, ep_id: examPaperId},
            success: function (data) {
                if (data.code == 0) {
                    ASBuilder.init(data.result.as, data.result.paper);
                } else {

                }
            },
            error: function () {
                //TODO 生成失败
            }
        });
    })
</script>
</body>
</html>