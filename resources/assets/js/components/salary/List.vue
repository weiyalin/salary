<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item to="/exam/list">工资表管理</el-breadcrumb-item>
                <el-breadcrumb-item>全部工资表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-form :inline="true" label-position="right">
            <el-form-item label="发放月份">
                <el-date-picker
                        v-model="fix_send_month"
                        type="month"
                        size="small"
                        @change="send_month_change"
                        placeholder="选择发放月份">
                </el-date-picker>
            </el-form-item>
            <el-form-item label="工资表名称">
                <el-input v-model="searchParams.salaryName" placeholder="输入工资表名称查询" size="small"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="search" @click="onSearchClick"
                           :loading="searching" size="small">查询
                </el-button>
            </el-form-item>
        </el-form>
        <el-table
                class="salary-table"
                :data="data"
                border>
            <el-table-column
                    type="index"
                    width="40"
                    label="#">
            </el-table-column>
            <el-table-column
                    min-width="150"
                    label="工资表名称" prop="name">
            </el-table-column>
            <el-table-column
                    min-width="80"
                    label="发放月份" prop="send_title">
            </el-table-column>
            <el-table-column
                    min-width="80"
                    label="全部人员" prop="total_person">
            </el-table-column>

            <el-table-column
                    min-width="70"
                    label="已发放" prop="send_count">
            </el-table-column>

            <el-table-column
                    min-width="70"
                    label="未发放" prop="unsend_count">
                <template scope="scope">
                    <el-tag v-if="scope.row.unsend_count>0" type="warning">{{scope.row.unsend_count}}</el-tag>
                    <span v-else>{{scope.row.unsend_count}}</span>
                </template>
            </el-table-column>

            <el-table-column
                    min-width="80"
                    label="反馈人数">
                <template scope="scope">
                    <el-tag v-if="scope.row.unsend_count>0" type="danger">{{scope.row.feedback_count}}</el-tag>
                    <span v-else>{{scope.row.feedback_count}}</span>
                </template>
            </el-table-column>

            <el-table-column
                    min-width="80"
                    label="添加人" prop="create_user_name">
            </el-table-column>

            <el-table-column
                    min-width="130"
                    label="添加时间">
                <template scope="scope">
                    {{new Date(scope.row.create_time).format('yyyy-MM-dd hh:mm')}}
                </template>
            </el-table-column>
            <el-table-column
                    min-width="80"
                    align="center"
                    label="密码" prop="create_time">
                <template scope="scope">
                    <el-button v-if="scope.row.password==''"
                               size="mini"
                               type="text"
                               @click="onSetPasswordClick(scope.row)">设置密码
                    </el-button>
                    <span v-else><i class="ion-ios-locked-outline"></i></span>
                </template>
            </el-table-column>

            <el-table-column label="操作" min-width="130">
                <template scope="scope">
                    <el-button
                            size="mini"
                            @click="onDetailClick(scope.row)">查看工资条
                    </el-button>

                    <el-button
                            size="mini"
                            @click="onDeleteClick(scope.row.id)"><i class="el-icon-delete"></i>
                    </el-button>

                </template>
            </el-table-column>
        </el-table>
        <el-pagination
                style="padding: 1rem 0;"
                @current-change="current_change"
                @size-change="size_change"
                :page-sizes="[10, 20, 50, 100, 150, 200]"
                :page-size="searchParams.page_size"
                layout="total, sizes, prev, pager, next, jumper"
                :total="searchParams.paginate_total">
        </el-pagination>
        <el-dialog title="设置工资条密码" :visible.sync="passwordSetDialogVisible" size="tiny">
            <el-form @submit.prevent.native="onSetPasswordOkClick">
                <el-form-item label="密码">
                    <el-input v-model="salary_password" placeholder="请输入查看工资条密码"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="passwordSetDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="onSetPasswordOkClick" :loading="passwordSetLoading"
                           :disabled="salary_password.trim().length==0">确 定
                </el-button>
            </div>
        </el-dialog>

        <el-dialog title="工资条密码" :visible.sync="passwordAuthDialogVisible" size="tiny"
                   @close="onPasswordAuthDialogClose">
            <el-form @submit.prevent.native="onAuthPasswordClick">
                <el-form-item label="密码">
                    <el-input v-model="salary_password" type="password" placeholder="请输入查看工资条密码"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="passwordAuthDialogVisible = false;">取 消</el-button>
                <el-button type="primary" @click="onAuthPasswordClick" :loading="passwordAuthLoading"
                           :disabled="salary_password.trim().length==0">确 定
                </el-button>
            </div>
        </el-dialog>
    </div>
</template>
<style>
    .salary-table td, .salary-table th {
        font-size: 12px !important;
        height: 30px !important;
    }

    .salary-table .cell {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }
</style>
<script>

    export default {
        data() {
            return {
                salary: {},
                data: [],
                searching: false,
                passwordSetDialogVisible: false,
                passwordSetLoading: false,
                salary_password: '',
                passwordAuthDialogVisible: false,
                passwordAuthLoading: false,

                fix_send_month: '',//TODO date组件有问题,特加此变量
                searchParams: {
                    sendMonth: '',
                    salaryName: '',
                    // 分页
                    page: 1,
                    page_size: 50,
                    paginate_total: 0
                },
            }
        },
        methods: {
            send_month_change: function (val) {
                this.searchParams.sendMonth = val;
            },
            size_change: function (size) {
                this.searchParams.page_size = size;
                this.search();
            },
            current_change: function (page) {
                this.searchParams.page = page;
                this.search();
            },
            onDetailClick(row){
                this.salary = row;
                if (this.salary.password.length == 0) {
                    this.goSalaryDetail();
                } else {
                    //需要密码
                    let self = this;
                    self.$http.post('/salary/check_password', {
                        id: this.salary.id
                    }).then(function (res) {
                        var data = res.data;
                        if (data.code == 0) {
                            if (data.result) {
                                //不需要验证密码
                                self.goSalaryDetail();
                            } else {
                                self.passwordAuthDialogVisible = true;
                            }
                        } else {
                            self.$message({
                                title: '提示',
                                message: data.msg,
                                type: 'warning'
                            });
                        }
                    })

                }
            },
            goSalaryDetail(){
                this.$router.push(`/salary/detail/${this.salary.id}`);
            },
            onSetPasswordClick(row){
                this.salary = row;
                this.passwordSetDialogVisible = true;
            },
            onSetPasswordOkClick(){
                let self = this;
                self.passwordSetLoading = true;
                self.$http.post('/salary/set_password', {
                    id: this.salary.id,
                    password: self.salary_password
                }).then(function (res) {
                    var data = res.data;
                    if (data.code == 0) {
                        self.$message({
                            title: '提示',
                            message: '密码设置成功',
                            type: 'success'
                        });
                        self.passwordSetDialogVisible = false;
                        self.salary_password = '';
                        self.search();
                    } else {
                        self.$message({
                            title: '提示',
                            message: data.msg,
                            type: 'warning'
                        });
                    }
                    self.passwordSetLoading = false;
                })
            },
            onAuthPasswordClick(){
                let self = this;
                self.passwordAuthLoading = true;
                self.$http.post('/salary/auth_password', {
                    id: this.salary.id,
                    password: self.salary_password
                }).then(function (res) {
                    var data = res.data;
                    if (data.code == 0) {
                        self.passwordAuthDialogVisible = false;
                        self.salary_password = '';
                        self.goSalaryDetail();
                    } else {
                        self.$message({
                            title: '提示',
                            message: data.msg,
                            type: 'warning'
                        });
                    }
                    self.passwordAuthLoading = false;
                })
            },
            onPasswordAuthDialogClose(){
                this.salary_password = '';
            },
            onDeleteClick(id){
                let self = this;
                this.$confirm('确认删除吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.$http.post('/salary/delete', {id: id}).then(function (res) {
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
                }).catch(function () {
                })
            },
            onSearchClick(){
                this.searchParams.page = 1;
                this.search();
            },
            search(){
                this.searching = true;
                this.$http.get("/salary/list", {
                    params: {
                        page_size: this.searchParams.page_size,
                        page: this.searchParams.page,
                        salary_name: this.searchParams.salaryName,
                        send_month: this.searchParams.sendMonth,
                    }
                }).then(function (response) {
                    this.data = response.data.result.data;
                    this.searchParams.paginate_total = response.data.result.total;
                    this.searching = false;
                }).catch(function (error) {
                    this.$message.error("查询失败");
                    this.searching = false;
                })
            }
        },
        mounted(){
            this.search();
        }
    }

</script>