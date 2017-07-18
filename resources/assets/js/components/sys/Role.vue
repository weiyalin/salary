<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>系统管理</el-breadcrumb-item>
                <el-breadcrumb-item>权限管理</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <router-link to="/role/edit">
            <el-button type="primary" size="small"><i class="el-icon-plus"></i> 添加角色</el-button>
        </router-link>
        <el-table
                :data="tableData"
                border
                style="width: 100%; margin-top:10px;">
            <el-table-column
                    type="index"
                    width="50"
                    label="#">
            </el-table-column>
            <el-table-column
                    label="角色名称">
                <template scope="scope">
                    <span style="margin-left: 10px">{{ scope.row.role_name }}</span>
                </template>
            </el-table-column>
            <el-table-column
                    label="角色类型" title="内置角色无法删除和更改">
                <template scope="scope">
                    <span style="margin-left: 10px">{{ scope.row.type==0 ? '内置角色' : '普通角色' }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-button
                            size="small"
                            type="primary"
                            @click="handleEdit(scope.row.id)" icon="edit">
                    </el-button>
                    <el-button
                            size="small"
                            type="danger"
                            icon="delete"
                            v-if="scope.row.type != 0"
                            @click="handleDelete(scope.row.id)"></el-button>
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
                pageSize : 50,
                total : 0,
            }
        },
        methods: {
            getData(){
                var query = {page:this.page,pageSize:this.pageSize};
                this.$http.get('/role/get_role_list_paginate',{params:query}).then(function(res){
                    var data = res.data;
                    this.total = data.total;
                    this.tableData = data.roles;
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
                this.$router.push(`/role/edit?id=${id}`);
            },
            handleDelete(id) {
                var self = this;
                this.$confirm('确认删除？','提示',{
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function(){
                    self.$http.post('/role/delete',{id:id}).then(function(res){
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
            }
        },
        mounted(){
            this.getData();
        }
    }
</script>