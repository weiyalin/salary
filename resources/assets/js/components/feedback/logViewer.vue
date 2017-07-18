<template>
    <div>
        <!--<iframe src="/log-viewer" :height="height" width="100%" border="0"></iframe>-->
    </div>
</template>
<script>
    export default{
        data(){
            return {
                height : 500,
            }
        },
        mounted(){
            window.open("http://admin.gm365.cc/log-viewer");
            /*var content = document.getElementsByClassName('el-row')[0];
            var winHeight = content.clientHeight;
            this.height = winHeight-30;*/
        }
    }
</script>
