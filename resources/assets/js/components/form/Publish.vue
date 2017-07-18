<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>表单</el-breadcrumb-item>
                <el-breadcrumb-item>表单发布</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-steps :space="140" :active="2" :center="true" style="margin-top:-73px;display:list-item;">
            <el-step class="to-edit step_edit" title="编辑" icon="edit"></el-step>
            <el-step class="step_upload" title="发布" icon="upload"></el-step>
        </el-steps>
        <el-row v-loading.body='loading'>
            <el-col :span="24">
                <div style="padding-bottom:40px;">
                    <div class="publish-title">新的表单</div>
                    <el-row type="flex" class="row-bg" justify="center">
                        <el-col :span="18">
                            <div style="margin-left:80px;">
                                <el-input class="read" style="font-size:18px;" placeholder="请输入发布地址" v-model="url">
                                    <template slot="prepend">http://</template>
                                </el-input>
                            </div>
                        </el-col>
                        <el-col :span="2" class="publist-button">
                            <div id="btn" class="js-copy background" :data-clipboard-text="'http://' + url">复制</div>
                        </el-col>
                        <el-col :span="2" class="publist-button">
                            <div @click="getOpen" class="background">打开</div>
                        </el-col>
                    </el-row>
                    <div id="qrcode" style="float: right;position: relative;top: -95px;"></div>
                    <el-row type="flex" class="row-bg" justify="center" style="margin-top:10px;">
                        <el-col :span="22">
                            <div style="float:right;" class="social-share">分享：</div>
                        </el-col>
                    </el-row>
                </div>
                <div>
                    <div style="padding: 0 0 10px 30px;border-bottom:1px solid #e5e9f2;">权限设置</div>
                    <div style="margin:20px 0 30px 60px;">
                    <div style="margin-bottom: 10px;">
                        <div style="width:365px;float:left;">是否需要登录</div>
                        <el-switch style="" v-model="is_login" @change="login_change" on-text="开启" off-text="关闭" on-color="#408080" off-color="#c6d1de"></el-switch>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div style="width:365px;float:left;">每人只能填写一次（根据用户的微信授权或IP地址判断）</div>
                        <el-switch style="" v-model="is_only" @change="only_change" on-text="开启" off-text="关闭" on-color="#408080" off-color="#c6d1de"></el-switch>
                    </div>
                    <div>
                        <div style="width:365px;float:left;">仅在微信中收集数据</div>
                        <el-switch style="" v-model="is_wx" @change="wx_change" on-text="开启" off-text="关闭" on-color="#408080" off-color="#c6d1de"></el-switch>
                    </div>
                    </div>                  
                </div>
                <div>
                    <div style="padding: 0 0 10px 30px;border-bottom:1px solid #e5e9f2;">分享设置</div>
                    <el-button style="margin:20px 0 30px 60px;" @click="wxshow = true"><i class="ion-chatbubbles"></i><span style="margin-left:5px;">微信分享设置</span></el-button>
                </div>
                <div v-if="enable == 1">
                    <div style="padding: 0 0 10px 30px;border-bottom:1px solid #e5e9f2;">发送通知</div>
                    <el-button style="margin:20px 0 30px 60px;" @click="chshow = true"><i class="ion-ios-people"></i><span style="margin-left:5px;">选择接收人</span></el-button>
                </div>
                <div>
                    <div style="padding: 0 0 10px 30px;border-bottom:1px solid #e5e9f2;">导出</div>
                    <div style="margin:10px 60px 0;font-size:12px;color:#868686;">将表单导出为word文档</div>
                    <a :href="'/form/export?id=' + id" style="color:#000;">
                        <el-button style="margin:10px 0 30px 60px;"><i class="ion-ios-cloud-download"></i><span style="margin-left:5px;">导出表单</span></el-button>
                    </a>
                </div>
            </el-col>
        </el-row>

        <el-dialog title="微信分享设置" v-model="wxshow" class="web_wxshare">
            <wxshare :uri="uri" @on-show="onShowWx" :show="wxshow"></wxshare>
        </el-dialog>

        <el-dialog title="选择接收人" size="large" v-model="chshow">
            <choose :uri="uri" @on-show="onShowCh" :show="chshow"></choose>
        </el-dialog>
        <!-- css&js -->
        <remote-link href="/css/share.min.css"></remote-link>
        <remote-script src="/js/jquery.share.min.js"></remote-script>
        <remote-script src="/js/qrcode.min.js"></remote-script>
        <remote-script src="/js/clipboard.min.js"></remote-script>
    </div>
</template>
<style scoped>
    .publish-title {
        padding: 20px 20px;
        font-size: 26px;
        text-align: center;
        color: #408080;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .publist-button{
        width: 40px;
        left: -80px;
        font-size:14px;
        position: relative;
        line-height: 36px;
        cursor: pointer;
    }

    .background{
        margin-top: 1px;
        background-color: #fff;
        height:34px;
        margin-right: 2px;
    }
</style>
<script>
    import wxshare from './Wxshare.vue'
    import choose from './Choose.vue'

    export default {
        components: {
            wxshare,
            choose,
        },
        data() {
            return {
                id: this.$route.query.id,
                url: "",
                uri: "",
                wxshow: false,
                chshow: false,

                loading: false,
                enable: 0,

                is_login: false,
                is_only: false,
                is_wx: false,
            }
        },
        methods: {
            get_data(){
                this.loading = true;
                this.$http.post('/form/release/get', {id:this.id}).then(function (response)  {
                    let data = response.data;
                    this.loading = false;
                    if(data.code == 0){
                        if(data.result.status == 0){
                            this.$router.push('/form/release?id=' + this.id)
                        }else{
                            this.enable = data.result.enable;
                            this.is_login = data.result.is_login == 1 ? true : false;
                            this.is_only = data.result.is_only == 1 ? true : false;
                            this.is_wx = data.result.is_wx == 1 ? true : false;
                            this.uri = data.result.url_code;
                            this.url = window.location.host + "/submit/" + this.uri;
                            init(this);
                        }
                    }else{
                        this.$notify({
                            type: 'warning',
                            message: '表单不存在，请查询后重新访问!',
                            duration: 2000,
                        });
                        this.$router.push('/form/list')
                    }
                })
            },
            getOpen(){
                window.open("http://" + this.url);
            },
            onShowWx(val){
                this.wxshow = false;
            },
            onShowCh(val){
                this.chshow = false;
            },
            edit(){
                this.$router.push('/form/edit?id=' + this.id)
            },
            login_change(val){
                let num = val ? 1 : 0;
                this.$http.post('/form/login/set', {id:this.id, num:num, type:1}).then(function (response)  {
                    let data = response.data;
                    if(data.code == 0){
                        this.$notify({
                            type: 'success',
                            message: '设置成功!',
                            duration: 2000,
                        });
                    }else{
                        this.$notify({
                            type: 'warning',
                            message: '设置失败，请刷新页面重新设置!',
                            duration: 2000,
                        });                        
                    }
                })
            },
            only_change(val){
                let num = val ? 1 : 0;
                this.$http.post('/form/login/set', {id:this.id, num:num, type:2}).then(function (response)  {
                    let data = response.data;
                    if(data.code == 0){
                        this.$notify({
                            type: 'success',
                            message: '设置成功!',
                            duration: 2000,
                        });                        
                    }else{
                        this.$notify({
                            type: 'warning',
                            message: '设置失败，请刷新页面重新设置!',
                            duration: 2000,
                        });                        
                    }
                })
            },
            wx_change(val){
                let num = val ? 1 : 0;
                this.$http.post('/form/login/set', {id:this.id, num:num, type:3}).then(function (response)  {
                    let data = response.data;
                    if(data.code == 0){
                        this.$notify({
                            type: 'success',
                            message: '设置成功!',
                            duration: 2000,
                        });                        
                    }else{
                        this.$notify({
                            type: 'warning',
                            message: '设置失败，请刷新页面重新设置!',
                            duration: 2000,
                        });                        
                    }
                })
            }            
        },
        mounted() {
            this.get_data();

            var self = this
            $('.to-edit .el-step__head').on("click", function(){
                self.edit();
            });
            $('.to-edit .el-step__title').on("click", function(){
                self.edit();
            });
        }
    }

    function init(self){
        new QRCode(document.getElementById('qrcode'), {
            text: 'http://' + self.url,
            width: 150,
            height: 150,
            colorDark : '#000000',
            colorLight : '#ffffff',
            correctLevel : QRCode.CorrectLevel.H
        });

        $(".read").find("input").attr("readonly","readonly")

        var btn = document.getElementById('btn');
        var clipboard = new Clipboard(btn);//实例化

        //复制成功执行的回调，可选
        clipboard.on('success', function(e) {
            self.$notify({
                type: 'success',
                message: '复制成功！',
                duration: 2000,
            });
        });

        var $config = {
            url                 : 'http://' + self.url, // 网址，默认使用 window.location.href
            source              : '', // 来源（QQ空间会用到）, 默认读取head标签：<meta name="site" content="http://overtrue" />
            title               : '', // 标题，默认读取 document.title 或者 <meta name="title" content="share.js" />
            description         : '', // 描述, 默认读取head标签：<meta name="description" content="PHP弱类型的实现原理分析" />
            image               : '', // 图片, 默认取网页中第一个img标签
            sites               : ['qzone', 'qq', 'weibo','wechat'], // 启用的站点
            disabled            : ['douban','google', 'facebook', 'twitter'], // 禁用的站点
            wechatQrcodeTitle   : "请打开微信扫一扫", // 微信二维码提示文字
            wechatQrcodeHelper  : '<p>微信里点“发现”，扫一下</p><p>二维码便可将本文分享至朋友圈。</p>',
        };

        $('.social-share').share($config);
    }
</script>