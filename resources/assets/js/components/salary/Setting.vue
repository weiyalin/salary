<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>工资条管理</el-breadcrumb-item>
                <el-breadcrumb-item>工资条设置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row>
            <el-form ref="form" label-width="150px">
                <el-form-item label="工资通知标题">
                   <el-row>
                       <el-col :span="12">
                           <el-input
                                   type="textarea"
                                   :rows="2"
                                   placeholder="请输入内容"
                                   :maxlength="60"
                                   v-model="title">
                           </el-input>
                       </el-col>
                       <el-col :span="12">
                            <div class="tips">	默认提示'yyyy年M月工资发放通知','yyyy年'代表年份 'M月'
                                代表月份请保留,其他可定义标题最多60个字。 </div>
                       </el-col>

                   </el-row>
                </el-form-item>
                <el-form-item label="工资通知摘要	">
                    <el-row>
                        <el-col :span="12">
                            <el-input
                                    type="textarea"
                                    :rows="2"
                                    placeholder="请输入内容"
                                    :maxlength="60"
                                    v-model="caption">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <div class="tips">	默认提示:'yyyy年M月工资已发放,请注意查收,有问题请在工资条中进行反馈',可根据实际进行修改. 摘要最多100个字
                            </div>
                        </el-col>

                    </el-row>
                </el-form-item>
                <el-form-item label="通知图片">
                    <el-row>
                        <el-col :span="12">
                            <el-upload
                                    class="avatar-uploader"
                                    name="files"
                                    :data="csrf_token"
                                    action="/salary/notify_upload"
                                    :show-file-list="false"
                                    :on-success="handleAvatarSuccess"
                                    :before-upload="beforeAvatarUpload">
                                <img v-if="imageUrl" :src="imageUrl" class="avatar">
                                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                        </el-col>
                        <el-col :span="12">
                            <div class="tips">	通知图片为发送通知时默认展示的图片,可以替换成公司自己的Logo或自制作的图片,不设置显示系统默认图片.
                                为达到更好效果,图片尺寸建议350(宽)*160(高);
                            </div>
                        </el-col>
                   </el-row>
                </el-form-item>

                <el-form-item label="工资条关怀语">
                    <el-row>
                        <el-col :span="12">
                            <el-input
                                    type="textarea"
                                    :rows="2"
                                    placeholder="请输入内容"
                                    :maxlength="100"
                                    v-model="content">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <div class="tips">		在发放工资条上添加关怀语,如祝生活愉快,工作顺利等,最多100个字
                            </div>
                        </el-col>

                    </el-row>
                </el-form-item>

                <el-form-item label="开启反馈">
                    <el-row>
                        <el-col :span="12">
                            <el-checkbox v-model="is_feedback"></el-checkbox>
                        </el-col>
                        <el-col :span="12">
                            <div class="tips">		开启后,可对工资条有疑问进行反馈,默认开启
                            </div>
                        </el-col>

                    </el-row>
                </el-form-item>

                <el-form-item label="开启销毁">
                    <el-row>
                        <el-col :span="12">
                            <el-checkbox v-model="is_destroy" ></el-checkbox>
                        </el-col>
                        <el-col :span="12">
                            <div class="tips">		开启后,员工可销毁接收到的工资条,销毁后不能查看,默认不开启.
                            </div>
                        </el-col>

                    </el-row>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="save">保存设置</el-button>
                </el-form-item>


            </el-form>

        </el-row>
    </div>
</template>
<style scoped>
    .tips {
        margin-left:10px;
        color:#888888
    }

    /*.avatar-uploader .el-upload {*/
        /*border: 1px dashed #d9d9d9;*/
        /*border-radius: 6px;*/
        /*cursor: pointer;*/
        /*position: relative;*/
        /*overflow: hidden;*/
    /*}*/
    /*.avatar-uploader .el-upload:hover {*/
        /*border-color: #20a0ff;*/
    /*}*/
    /*.avatar-uploader-icon {*/
        /*font-size: 28px;*/
        /*color: #8c939d;*/
        /*width: 178px;*/
        /*height: 178px;*/
        /*line-height: 178px;*/
        /*text-align: center;*/
    /*}*/
    .avatar {
        width: 350px;
        height: 160px;
        display: block;
    }

</style>

<script>
    import Form from './form'

    export default {

        data(){
            return {
                imageUrl: '',

                csrf_token: {
                    _token: Laravel.csrfToken
                },

                title:'',
                caption:'',
                pic:'',
                content:'',
                is_feedback:true,
                is_destroy:false
            }
        },
        methods: {
            init: function(){
                var self = this;
                let form = new Form();
                let params = {}

                form.get_setting(params,function(result,err){
                    if(result){
                        self.title = result.salary_notify_title;
                        self.caption = result.salary_notify_caption;
                        self.pic = result.salary_notify_pic;
                        self.imageUrl = '/salary/notify_img?name='+self.pic;
                        self.content = result.salary_care_content;
                        self.is_feedback = result.is_enable_feedback == 1;
                        self.is_destroy = result.is_enable_destroy == 1;


                    }
                    else {
                        self.$message.error(err);
                    }
                })
            },

            save:function(){
                let self = this;

                if(self.title.length == 0){
                    self.$message({
                        message: '标题不能为空',
                        type: 'warning'
                    });
                    return;
                }
                if(self.caption.length == 0){
                    self.$message({
                        message: '摘要不能为空',
                        type: 'warning'
                    });
                    return;
                }
                if(self.pic.length == 0){
                    self.$message({
                        message: '图片不能为空',
                        type: 'warning'
                    });
                    return;
                }



                let params = {
                    title:self.title,
                    caption:self.caption,
                    pic:self.pic,
                    content:self.content,
                    is_feedback:self.is_feedback,
                    is_destroy:self.is_destroy
                }

                let form = new Form();
                form.save_setting(params,function(result,err){
                    if(result){
                        self.$message({
                            message: result,
                            type: 'success'
                        });
                    }
                    else {
                        self.$message.error(err);
                    }
                })

            },
            handleAvatarSuccess(res, file) {
                if(res.code == 0){
                    this.pic = res.result;
                    this.imageUrl = '/salary/notify_img?name='+this.pic;
                    ///this.imageUrl = URL.createObjectURL(file.raw);
                }

            },
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg' || file.type === 'image/png';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG) {
                    this.$message.error('上传头像图片只能是 JPG 格式!');
                }
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isJPG && isLt2M;
            },

        },
        mounted() {
            this.init();
        }


        }
</script>