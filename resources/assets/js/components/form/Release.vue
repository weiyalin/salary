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
                    <div class="publish-icon">
                        <i class="el-icon-circle-check"></i>
                    </div>
                    <div class="publish-font" style="font-size:26px;color:#13CE66;">
                        <span>新的表单</span>
                    </div>
                    <div class="publish-font" style="font-size:26px;margin-top:30px;color:#000000;">
                        <span>一切就绪，可以发布啦！</span>
                    </div>
                    <div class="publish-font" style="font-size:14px;color:#AAAAAA;">
                        <span>点击发布按钮，生成表单的访问链接</span>
                    </div>
                    <div style="margin:30px;text-align:center;">
                        <el-button type="primary" @click="publish">发布表单</el-button>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style scoped>
    .publish-icon {
        padding: 20px 20px;
        font-size: 50px;
        text-align: center;
        color: #408080;
    }

    .publish-font {
        text-align: center;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
<script>
    export default {
        data() {
            return {
                id: this.$route.query.id,
                loading: false
            }
        },
        methods: {
            publish(){
                this.loading = true;
                this.$http.post('/form/release/set', {id:this.id}).then(function (response)  {
                    let data = response.data;
                    this.loading = false;
                    if(data.code == 0){
                        this.$router.push('/form/publish?id=' + this.id)
                    }
                })
            },
            get_data(){
                this.loading = true;
                this.$http.post('/form/release/get', {id:this.id}).then(function (response)  {
                    let data = response.data;
                    this.loading = false;
                    if(data.code == 0){
                        if(data.result.status != 0){
                            this.$router.push('/form/publish?id=' + this.id)
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
            edit(){
                this.$router.push('/form/edit?id=' + this.id)
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
</script>