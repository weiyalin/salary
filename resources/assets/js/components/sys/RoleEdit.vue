<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>系统管理</el-breadcrumb-item>
                <el-breadcrumb-item :to="{path:'/role/list'}">权限管理</el-breadcrumb-item>
                <el-breadcrumb-item>编辑</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row>
            <el-col :span="10">
                <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
                    <el-form-item label="角色名称" prop="name">
                        <el-input v-model="ruleForm.name" size="small"></el-input>
                    </el-form-item>
                    <el-form-item label="权限">
                        <el-tree ref="manage_tree"
                                 :data.sync="menu"
                                 :props="defaultProps"
                                 :node-key="defaultProps.nodeKey"
                                 :auto-expand-parent=true
                                 :default-expanded-keys="role_code"
                                 :default-checked-keys="role_code"
                                 show-checkbox
                                 :highlight-current=true>
                        </el-tree>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submitForm()">提 交</el-button>
                        <router-link to="/role/list">
                            <el-button>取 消</el-button>
                        </router-link>
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
                ruleForm : {
                    id : 0,
                    name : ''  //角色名称
                },
                type : 1,    //角色类型
                rules: {
                    name: [
                        {required: true, message: '请输入角色名称',trigger:'blur'}
                    ]
                },
                defaultProps : {
                    children : 'children',
                    label : 'name',
                    nodeKey : 'code'
                },
                menu : [],
                role_code : []
            }
        },
        methods :{
            getData(){
                this.$http.get('/role/edit',{params:{id:this.ruleForm.id}}).then(function(res){
                    var data = res.data;
                    this.role_code = data.role_code;
                    this.ruleForm.name = data.info.role_name;
                    this.type = data.info.type;
                    this.menu = data.menu;
                })
            },
            submitForm(){
                var self = this;
                this.$refs['ruleForm'].validate(function(valid){
                    if(valid){
                        var data = self.ruleForm;
                        var checkedNodes = self.$refs['roleAuth'].getCheckedNodes();
                        var code = [];
                        $.each(checkedNodes,function(i,v){
                            if(v.code.length == 3){
                                code.push(v.code);
                            }else if(v.code.length == 6){
                                if($.inArray(v.code.substr(0,3),code) == -1){
                                    code.push(v.code);
                                }
                            }
                        });
                        data.code = code;
                        self.$http.post('/role/edit_save',data).then(function(res){
                            var data = res.data;
                            var title = data.status==0 ? '成功' : '失败';
                            var type = data.status ==0 ? 'success' : 'warning';
                            this.$message({
                                title: title,
                                message: data.msg,
                                type: type
                            });
                            if(data.status==0){
                                self.$router.push('/role/list');
                            }
                        });
                    }
                })
            }
        },
        mounted() {
            this.ruleForm.id = this.$route.query.id;
            this.getData();
        }
    }
</script>
