<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>用户模块</el-breadcrumb-item>
                <el-breadcrumb-item>用户管理</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <div class="page-content">
            <el-card class="box-card">
                <el-form label-width="80px">
                    <el-form-item label="反馈内容">
                        <span>{{feedback.message}}</span>
                    </el-form-item>
                </el-form>
                <el-form label-width="80px">
                    <el-form-item label="UserAgent">
                        <span>{{feedback.useragent}}</span>
                    </el-form-item>
                </el-form>
                <el-form label-width="80px">
                    <el-form-item label="客户端IP">
                        <span>{{feedback.ip}}</span>
                    </el-form-item>
                </el-form>
                <el-form label-width="80px">
                    <el-form-item label="创建时间">
                        <span>{{feedback.created_at}}</span>
                    </el-form-item>
                </el-form>
                <el-form label-width="80px">
                    <el-form-item label="屏幕截图">
                        <div style="width:100%; overflow:auto;">
                            <img :src="feedback.url" />
                        </div>
                    </el-form-item>
                </el-form>
            </el-card>
            <el-card class="box-card" style="margin-top:10px;">
                <div id="SOHUCS" :sid="feedback.id"></div>
            </el-card>
        </div>
    </div>
</template>
<style scoped>

</style>

<script>

    export default{
        data(){
            return{
                feedback:{
                    id:0,
                    message:'',
                    url:'',
                    useragent:'',
                    ip:'',
                    created_at:''
                }
            }
        },
        methods:{
            load(){
                var appid = 'cysQ48fLL';
                var conf = 'prod_293ec17cf724f0e34d4c7432ececce0a';
                var width = window.innerWidth || document.documentElement.clientWidth;
                if (width < 960) {
                    window.document.write('<script id="changyan_mobile_js" charset="utf-8" type="text/javascript" src="http://changyan.sohu.com/upload/mobile/wap-js/changyan_mobile.js?client_id=' + appid + '&conf=' + conf + '"><\/script>');
                } else {
                    var loadJs=function(d,a){
                        var c=document.getElementsByTagName("head")[0]||document.head||document.documentElement;
                        var b=document.createElement("script");
                        b.setAttribute("type","text/javascript");
                        b.setAttribute("charset","UTF-8");
                        b.setAttribute("src",d);
                        if(typeof a==="function"){
                            if(window.attachEvent){
                                b.onreadystatechange=function(){
                                    var e=b.readyState;
                                    if(e==="loaded"||e==="complete"){
                                        b.onreadystatechange=null;
                                        a()
                                    }
                                }
                            }
                            else{
                                b.onload=a
                            }
                        }
                        c.appendChild(b)
                    };
                    loadJs("http://changyan.sohu.com/upload/changyan.js",function(){window.changyan.api.config({appid:appid,conf:conf})});
                    }
            }
        },
        mounted: function(){
            this.load();
            //reload comment js
            //this.uyan_init();

            var id = this.$route.params.id;
            console.log(id);
            var query = {id:id};
            this.$http.get('/feedback/detail',{params:query}).then(function(res){
                var result = res.data;
                if(result){
                    this.feedback.id = result.id;
                    this.feedback.message = result.message;
                    this.feedback.url = result.url;
                    this.feedback.useragent = result.useragent;
                    this.feedback.ip = result.ip;
                    this.feedback.created_at = result.created_at;

                }
                else {
                    this.feedback.id = 0;
                    this.feedback.message = '';
                    this.feedback.url = '';
                    this.feedback.useragent = '';
                    this.feedback.ip = '';
                    this.feedback.created_at = '';
                }

            })

        },
        components:{

        }
    }
</script>
