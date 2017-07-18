<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>用户模块</el-breadcrumb-item>
                <el-breadcrumb-item>用户管理</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <router-link to="/user/edit">
            <el-button type="primary" size="small">添加用户</el-button>
        </router-link>
        <el-table
                :data="tableData"
                border
                style="width: 100%; margin-top:10px;">
            <el-table-column
                    label="用户名">
                <template scope="scope">
                    <span style="margin-left: 10px">{{ scope.row.name }}</span>
                </template>
            </el-table-column>
            <el-table-column
                    label="电话">
                <template scope="scope">
                    {{ scope.row.phone }}
                </template>
            </el-table-column>
            <el-table-column
                    label="角色">
                <template scope="scope">
                    {{ scope.row.role_name }}
                </template>
            </el-table-column>
            <el-table-column
                    label="创建时间">
                <template scope="scope">
                    {{ scope.row.create_time | format_time }}
                </template>
            </el-table-column>
            <el-table-column label="操作" width="300">
                <template scope="scope">
                    <el-button
                            size="small"
                            type="primary"
                            @click="handleEdit(scope.row.id)">信息编辑</el-button>
                    <el-button
                        size="small"
                        type="warning"
                        @click="resetPwd(scope.row.id)">重置密码</el-button>
                    <el-button
                            size="small"
                            type="danger"
                            @click="handleDelete(scope.row.id)"> 删 除 </el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                @size-change="onPageSizeChange"
                @current-change="onPageChange"
                :current-page="page"
                :page-sizes="[10, 20, 50, 100]"
                :page-size="pageSize"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
        </el-pagination>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tableData: [],
                page : 1,
                pageSize : 10,
                total : 0,
            }
        },
        methods: {
            getData(){
                var query = {page:this.page,pageSize:this.pageSize};
                this.$http.get('/user/get_user_list_paginate',{params:query}).then(function(res){
                    var data = res.data;
                    this.total = data.total;
                    this.tableData = data.users;
                })
            },
            onPageChange(val){
                this.page = val;
                this.getData();
            },
            onPageSizeChange(val){
                this.pageSize = val;
                this.getData();
            },
            handleEdit(id) {
                this.$router.push(`/user/edit?id=${id}`);
            },
            handleDelete(id) {
                var self = this;
                this.$confirm('确认删除？','提示',{
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function(){
                    self.$http.post('/user/delete',{id:id}).then(function(res){
                        var data = res.data;
                        data.status==0? self.getData() : '';
                        var title = data.status==0 ? '成功' : '失败';
                        var type = data.status ==0 ? 'success' : 'warning';
                        this.$message({
                            title: title,
                            message: data.msg,
                            type: type
                        });
                    })
                }).catch(function(){})
            },
            resetPwd(id){
                var self = this;
                this.$confirm('密码将重置为“123456”，是否继续？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function(){
                    self.$http.post('/user/reset_pwd',{id:id}).then(function(res){
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
            }
        },
        mounted(){
            this.getData();
        }
    }
</script>