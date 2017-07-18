<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>用户模块</el-breadcrumb-item>
                <el-breadcrumb-item>用户管理</el-breadcrumb-item>
                <el-breadcrumb-item>编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <router-link to="/user/users">
            <el-button size="small"><i class="el-icon-caret-left"></i> 返回</el-button>
        </router-link>
        <el-row style="margin-top:15px;">
            <el-col style="width:400px;">
                <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
                    <el-form-item label="用户名" prop="name">
                        <el-input v-model="ruleForm.name"></el-input>
                    </el-form-item>
                    <el-form-item label="电话" prop="phone">
                        <el-input v-model="ruleForm.phone"></el-input>
                    </el-form-item>
                    <el-form-item label="角色" prop="role_id">
                        <el-select v-model="ruleForm.role_id" placeholder="请选择用户角色">
                            <el-option v-for="role in roles" :label="role.role_name" :value='role.id' ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="密码" v-if="!id">
                        <el-input value="123456" :disabled="true"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submitForm()">提交</el-button>
                        <el-button @click="resetPwd()" v-if="id" type="warning">重置密码</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                id : 0,
                roles : [],
                ruleForm :{
                    name : '',
                    phone : '',
                    role_id : ''
                },
                rules : {
                    name : [
                        {required : true, message : '请输入用户名',trigger : 'blur'},
                        {min:5 , max : 20, message : '长度在5~20位',trigger : 'blur'}
                    ],
                    role_id : [
                        {required : true, message : '请选择用户角色'}
                    ]
                }
            }
        },
        methods : {
            get_user_info(){
                this.$http.get('/user/get_user_info',{params:{id:this.id}}).then(function(res){
                    var info = res.data.info;
                    this.roles = res.data.roles;
                    this.ruleForm.id = info.id;
                    this.ruleForm.name = info.name;
                    this.ruleForm.phone = info.phone;
                    this.ruleForm.role_id = info.role_id;
                })
            },
            get_role_list(){
                this.$http.get('/user/get_role_list').then(function(res){
                    this.roles = res.data;
                })
            },
            resetPwd(){
                var self = this;
                this.$confirm('密码将重置为“123456”，是否继续？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function(){
                    self.$http.post('/user/reset_pwd',{id:self.id}).then(function(res){
                        var data = res.data;
                        var title = data.status==0 ? '成功' : '失败';
                        var type = data.status ==0 ? 'success' : 'warning';
                        this.$message({
                            title: title,
                            message: data.msg,
                            type: type
                        });
                    }).catch(function(){})
                }).catch(function(){})
            },
            submitForm(){
                var self = this;
                this.$refs['ruleForm'].validate(function(valid){
                    if(valid){
                        self.$http.post('/user/add',self.ruleForm).then(function(res){
                            var data = res.data;
                            var title = data.status==0 ? '成功' : '失败';
                            var type = data.status ==0 ? 'success' : 'warning';
                            this.$message({
                                title: title,
                                message: data.msg,
                                type: type
                            });
                            if(data.status==0){
                                self.$router.push('/user/users');
                            }
                        })
                    }
                });
            }
        },
        mounted() {
            this.id = this.$route.query.id;
            if(this.id){
                this.get_user_info();
            }else{
                this.get_role_list();
            }
        }
    }
</script>
