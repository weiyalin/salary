<template>
    <div>
        <pageHeader></pageHeader>
        <div style="/*position:fixed;*/top:60px;bottom:0;left:0;right:0;padding:20px 30px;;">
            <div class="gm-main-wrap">
                <div class="page-menu">
                    <pageMenu></pageMenu>
                </div>

                <div class="page-content">
                    <div style="padding:10px 20px 35px;background-color:#fff;">
                        <router-view></router-view>
                    </div>
                    <div style="clear:both"></div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
    import pageHeader from './components/layout/header.vue'
    import pageMenu from './components/layout/menu.vue'
    export default{
        components: {
            pageHeader,
            pageMenu
        },
/*        watch: {
            // 如果路由有变化，会再次执行该方法
            "$route":function(val){
                setTimeout(function(){
                    $(".page-content").css("height", "");
                    $(".page-menu").css("height", "");
                    var _mainBody = $(".page-content").height()
                    var _windonw = $(window).height();

                    if (_windonw - 100 > _mainBody) {
                        console.log("======")
                        console.log(_windonw)
                        console.log(_mainBody)
                        $(".page-content").css("height", _windonw - 100);
                        $(".page-menu").css("height", _windonw - 100);
                    }else{
                        console.log("+++++++")
                        console.log(_windonw)
                        console.log(_mainBody)
                        $(".page-content").css("height", _mainBody);
                        $(".page-menu").css("height", _mainBody);
                    }
                },500)
            }
        }*/
    }
</script>
