<template>
    <div>
        <!--头部位置-->
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item to="/apply/list">申请管理</el-breadcrumb-item>
                <el-breadcrumb-item>申请体验</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <!--搜索-->
        <el-form :inline="true" label-position="right">
            <el-form-item label="姓名或公司">
                <el-input v-model="searchParams.keyword" placeholder="输入姓名或公司查找"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" icon="search" @click="onSearchClick"
                           :loading="searching">查询
                </el-button>
            </el-form-item>

            <el-form-item>
                <el-select v-model="searchParams.status" placeholder="请选择" @change="onOptionChange">
                    <el-option
                            v-for="item in options"
                            :key="item.value"
                            :label="item.name"
                            :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>

        </el-form>
        <!--列表-->
        <el-row>
            <el-col :span="24">
                <!--表格-->
                <el-table
                        :data="data"
                        @current-change="handleCurrentChange"

                        border>
                    <el-table-column
                            type="index"
                            label="#">
                    </el-table-column>

                    <el-table-column
                            width="150"
                            label="姓名" prop="person">
                    </el-table-column>

                    <el-table-column
                            width="300"
                            label="电话" prop="mobile">
                    </el-table-column>

                    <el-table-column
                            label="公司" prop="company">
                    </el-table-column>

                    <el-table-column
                            width="200"
                            label="申请时间">
                            <template scope="scope">
                                {{new Date(scope.row.create_time).format('yyyy-MM-dd hh:mm')}}
                            </template>
                    </el-table-column>

                    <el-table-column
                            width="100"
                            label="处理状态" prop="status">
                        <template scope="scope">
                            <el-tag v-if="scope.row.status==0" type="warning">未处理</el-tag>
                            <el-tag v-if="scope.row.status==1" type="success">已处理</el-tag>
                        </template>
                    </el-table-column>

                    <el-table-column label="操作" width="120">
                        <template scope="scope">
                            <el-button
                                    size="mini"
                                    @click="onDeleteClick(scope.row.id)"><i class="el-icon-delete"></i>
                            </el-button>
                        </template>
                    </el-table-column>

                </el-table>
                <!--分页-->
                <el-pagination
                        style="padding: 1rem 0;"
                        @current-change="current_change"
                        @size-change="size_change"
                        :page-sizes="[10, 20, 50, 100, 150, 200]"
                        :page-size="searchParams.page_size"
                        layout="total, sizes, prev, pager, next, jumper"
                        :total="searchParams.paginate_total">
                </el-pagination>
            </el-col>
        </el-row>
    </div>
</template>
<script>

    export default {
        data() {
            return {

                options:[],
                data: [],
                searching: false,

                searchParams: {
                    keyword: '',
                    status:2,   //0：未处理  1：已处理  2：全部
                    // 分页
                    page: 1,
                    page_size: 10,
                    paginate_total: 0
                },
            }
        },
        methods: {
            size_change: function (size) {
                this.searchParams.page_size = size;
                this.search();
            },
            current_change: function (page) {
                this.searchParams.page = page;
                this.search();
            },
            search(){
                this.searching = true;
                this.$http.get("/salary/apply_search", {
                    params: {
                        page_size: this.searchParams.page_size,
                        page: this.searchParams.page,
                        keyword: this.searchParams.keyword,
                        status: this.searchParams.status,
                    }
                }).then(function (response) {
                    this.data = response.data.result.data;
                    this.searchParams.paginate_total = response.data.result.total;
                    this.searching = false;
                }).catch(function (error) {
                    this.$message.error("查询失败");
                    this.searching = false;
                })
            },
            onSearchClick(){
                this.searchParams.page = 1;
                this.search();
            },
            onOptionChange(){
                this.searchParams.page = 1;
                this.search();
            },
            handleCurrentChange(val) {
                console.log(val);
            },
            onOptions(){
                var self = this;
                self.$http.get("/salary/apply_options", {params:{}
                }).then(function (response) {
                    if(response.data.code == 0){
                        self.options = response.data.result;
                    }
                }).catch(function (error) {
                    console.log(error);
                    self.$message.error(error);
                })
            },
            onDeleteClick(id){
                let self = this;
                this.$confirm('确认删除吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.$http.post('/salary/apply_delete', {id: id}).then(function (res) {
                        var data = res.data;
                        if (data.code == 0) {
                            self.$message({
                                title: '提示',
                                message: '删除成功',
                                type: 'success'
                            });
                            self.search();
                        } else {
                            self.$message({
                                title: '提示',
                                message: data.msg,
                                type: 'warning'
                            });
                        }
                    })
                }).catch(function (error) {
                    console.log(error);
                    self.$message.error(error);
                })
            },
        },
        mounted(){
            this.onOptions();
            this.search();
        }
    }

</script>