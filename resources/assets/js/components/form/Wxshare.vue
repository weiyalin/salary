<template>
    <el-row v-loading.body='loading' element-loading-text="loading_text">
        <el-col :span="24">
            <el-form :model="form" ref="form" label-width="100px">
                <el-form-item label="标题" style="width:725px;">
                    <el-input v-model="form.title"></el-input>
                </el-form-item>
                <el-form-item label="描述" style="width:725px;">
                    <el-input v-model="form.desc"></el-input>
                </el-form-item>
                <el-form-item label="图片">
                    <el-upload class="upload-demo wxshare" style="height:70px;" :accept="accept" :headers="headers" action="/upload" :on-success="handleSuccess" :on-remove="handleRemove" :file-list="list" list-type="picture-card">
                        <i class="el-icon-plus upload-slot"></i>
                    </el-upload>
                </el-form-item>
            </el-form>
            <div class="web_wxshare">
                <div class="web_share_style">
                    <div class="web_share_title">聊天窗口样式</div>
                    <div style="position: relative;">
                        <div class="web_share_head"></div>
                        <div class="web_share_con">
                            <div class="web_share_window">
                                <div class="web_share_over">{{form.title}}</div>
                                <div class="web_share_desc">
                                    <img class="web_share_img" :src="img">
                                    <div class="web_share_desc_con">{{form.desc}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="web_share_style" style="margin-left:5px;">
                    <div class="web_share_title">朋友圈样式</div>
                    <div style="position: relative;">
                        <div class="web_share_head"></div>
                        <div class="web_share_con">
                            <div class="web_share_name">伽马</div>
                            <div class="web_share_frind">
                                <img class="web_share_frind_img" :src="img">
                                <div class="web_share_frind_desc">{{form.title}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="web_share_form_button">
                <el-button type="primary" @click="submitForm">保存</el-button>
                <el-button @click="resetForm">取消</el-button>
            </div>
        </el-col>
    </el-row>
</template>
<style scoped>
    .upload-slot{
        position: relative;
        top: -35px;
    }

    .web_wxshare{
        margin-left: 61px;
        padding: 5px;
        margin-top: 20px;
        background-color: #F6F9FB;
        border: 1px solid #EEE;
        height: 153px;
        width: 672px;
    }

    .web_share_style{
        float: left;
        width: 327px;
        height: 140px;
        padding: 10px;
        background-color: #fff;
        border: 1px solid #D9DFE2;
    }

    .web_share_title{
        line-height: 20px;
        font-size: 15px;
        color: #848484;
        margin-bottom: 10px;
    }

    .web_share_con{
        position: relative;
        padding-left: 50px;
        padding-right: 20px;
    }

    .web_share_window{
        padding: 10px;
        margin-left: 5px;
        background-color: #fff;
        border: 1px solid #E5E5E5;
        border-radius: 5px;
    }

    .web_share_desc{
        position: relative;
        height: 45px;
        margin-top: 5px;
    }

    .web_share_img{
        position: absolute;
        top: 0;
        right: 0;
        width: 45px;
        height: 45px;
    }

    .web_share_over{
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .web_share_desc_con{
        padding-right: 60px;
        height: 45px;
        line-height: 15px;
        font-size: 12px;
        color: #999;
        overflow: hidden;
        white-space: pre-line;
        word-wrap: break-word;
    }

    .web_share_name{
        width: 100%;
        height: 20px;
        line-height: 20px;
        color: #2D8FD9;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .web_share_frind{
        position: relative;
        padding: 5px;
        margin-top: 5px;
        background-color: #F3F3F5;
    }

    .web_share_frind_img{
        position: absolute;
        top: 5px;
        left: 5px;
        width: 32px;
        height: 32px;
    }

    .web_share_frind_desc{
        height: 32px;
        padding-left: 37px;
        line-height: 32px;
        color: #000;
        overflow: hidden;
    }

    .web_share_form_button{
        text-align: center;
        margin-left: 32px;
        margin-top: 20px;
    }
</style>
<script>
    export default {
        data: function () {
            return {
                form: {
                    title: '新的表单',
                    desc: '',
                    img: ''
                },

                list: [],
                fileList: {

                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf"]').attr('content')},
                accept: "image/*",

                loading: false,
                loading_text: "",
                img: "../img/admin/share_desc.png",
            }
        },
        props: {
            uri: {
                type: String,
                default: ""
            },
            show: {
                type: Boolean,
                default: false
            },
        },
        watch:{
            list(val){
                if(val.length > 0){
                    $(".wxshare").find('.el-upload--picture-card').hide();
                }else{
                    setTimeout(function() {
                        $(".wxshare").find('.el-upload--picture-card').show()
                    },500)
                }
            },
            show(val){
                if(val){
                    this.getData();
                }
            }
        },
        methods: {
            getData(){
                this.loading = true,
                this.loading_text = "拼命加载中~",
                this.$http.post('/form/data/get', {uri:this.uri}).then(function (response)  {
                    let data = response.data.result;
                    this.loading = false,
                    this.form.title = data.share_title
                    this.form.desc = data.share_desc
                    this.form.img = data.share_pic
                    if(data.share_pic){
                        this.img = "http://" + window.location.host + "/img/get/" + data.share_pic
                        this.list = [{name: data.share_pic, url: 'http://' + window.location.host + "/img/get/" + data.share_pic}]
                    }
                })
            },
            submitForm(){
                this.loading = true,
                this.loading_text = "保存中~",
                this.$http.post('/form/update', {form: this.form, uri:this.uri}).then(function (response)  {
                    let data = response.data;
                    this.loading = false;
                    if(data.code == 0){
                        this.$emit("on-show", 1);
                        this.$notify({
                            type: 'success',
                            message: '保存成功!',
                            duration: 2000,
                        });
                    }else{
                        this.$notify({
                            type: 'error',
                            message: '保存失败!',
                            duration: 2000,
                        });
                    }
                })
            },
            handleSuccess(response, file, fileList){
                this.fileList = response.result
                this.form.img = response.result.name
                this.img = "http://" + window.location.host + "/img/get/" + response.result.name
                $(".wxshare").find('.el-upload--picture-card').hide();
            },
            handleRemove(file, fileList) {
                this.form.img = ""
                this.img = "../img/admin/share_desc.png"
                setTimeout(function() {
                    $(".wxshare").find('.el-upload--picture-card').show()
                },500)
            },
            resetForm(){
                this.$emit("on-show", 1);
            },
        },
        mounted() {
            this.getData();
        }
    }
</script>