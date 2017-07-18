<template>
    <div>
        <div class="gm-breadcrumb">
            <el-button type="text" class="gm-back" @click="$router.go(-1)"><i class="el-icon-arrow-left"></i>
            </el-button>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item to="/exam/list">工资表管理</el-breadcrumb-item>
                <el-breadcrumb-item>{{salary.name}}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-form :inline="true" label-position="right">
            <el-form-item label="发放状态">
                <el-select v-model="searchParams.send" placeholder="请选择" @change="params_change" size="small">
                    <el-option label="全部" value="-1"></el-option>
                    <el-option label="已发放" value="1"></el-option>
                    <el-option label="未发放" value="0"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="反馈状态">
                <el-select v-model="searchParams.feedback" placeholder="请选择" @change="params_change" size="small">
                    <el-option label="全部" value="-1"></el-option>
                    <el-option label="有反馈" value="1"></el-option>
                    <el-option label="无反馈" value="0"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="员工">
                <el-input v-model="searchParams.keyword" placeholder="输入姓名或工号" size="small"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="search" @click="onSearchClick"
                           :loading="searching" size="small">查找
                </el-button>
            </el-form-item>
        </el-form>
        <el-row>
            <el-col :span="6">
                <el-tree :data="tree_data" :props="defaultProps" @node-click="handleNodeClick"
                         :loading="treeLoading" highlight-current
                         style="margin-right:10px;min-height:300px;border-top: solid 5px rgb(209,227,229);"></el-tree>

            </el-col>
            <el-col :span="18">
                <div style="margin-bottom:10px;position:relative;">
                    <el-button size="small" @click="onSendClick" :disabled="selectedSalary.length==0 || sendingAll"
                               :loading="sendingSelected"><i
                            class="ion-android-person"/> 对选择人员发放工资条
                    </el-button>
                    <el-button size="small" @click="onSendAllClick" :loading="sendingAll" :disabled="sendingSelected"><i
                            class="ion-android-people"/> 全部人员
                    </el-button>
                    <el-button size="small" @click="onDeleteClick" :disabled="selectedSalary.length==0"><i
                            class="ion-android-delete"/> 删除选中工资条
                    </el-button>
                    <span style="display: inline-block;color:red;position:absolute;font-size:12px;bottom:0;right:0;"> 共{{salary.total_person}}人 已发放{{salary.send_count}}人 {{salary.feedback_count}}人反馈</span>
                </div>
                <el-table
                        class="salary-detail-table"
                        :data="data"
                        v-show="!isEmptyTplCols"
                        @selection-change="onSelectionChange"
                        border>
                    <el-table-column
                            type="selection"
                            width="30">
                    </el-table-column>
                    <el-table-column
                            type="index"
                            width="40"
                            fixed
                            label="#">
                    </el-table-column>

                    <el-table-column
                            width="80"
                            fixed
                            label="发放月份" prop="send_title">
                    </el-table-column>

                    <el-table-column
                            fixed
                            label="工号" prop="code">
                    </el-table-column>

                    <el-table-column
                            v-for="(val, key) in tplCols"
                            :fixed="val=='姓名'"
                            :label="val" :prop="key">
                    </el-table-column>

                    <el-table-column
                            width="70"
                            align="center"
                            label="发放状态">
                        <template scope="scope">
                            <el-tag v-if="scope.row.is_send==0" type="warning">未发放</el-tag>
                            <span v-else>已发放</span>
                        </template>
                    </el-table-column>

                    <el-table-column
                            label="员工反馈" prop="feedback_content">
                    </el-table-column>

                    <el-table-column
                            label="反馈回复" prop="reply_content">
                    </el-table-column>
                    <el-table-column
                            width="70"
                            label="是否已阅" align="center">
                        <template scope="scope">
                            <el-tag v-if="scope.row.is_read==0" type="warning">未阅</el-tag>
                            <span v-else>已阅</span>
                        </template>
                    </el-table-column>

                    <el-table-column
                            width="60"
                            align="center">
                        <template scope="scope">
                            <el-button
                                    size="small"
                                    type="text"
                                    @click="onReplyClick(scope.row)">回复
                            </el-button>
                        </template>
                    </el-table-column>

                </el-table>
                <el-pagination
                        v-show="!isEmptyTplCols"
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
        <el-dialog title="回复员工反馈" :visible.sync="replyDialogVisible" size="tiny">
            <el-form :model="salaryDetail" @submit.prevent.native="onReplyOkClick">
                <el-form-item label="员工反馈">
                    <el-tag v-if="salaryDetail.is_feedback==0" type="warning">无反馈</el-tag>
                    <p v-else>{{salaryDetail.feedback_content}}</p>
                </el-form-item>
                <el-form-item label="回复内容">
                    <el-input type="textarea" v-model="replyContent"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="onReplyCancelClick">取 消</el-button>
                <el-button type="primary" @click="onReplyOkClick" :loading="replyLoading"
                           :disabled="replyContent.trim().length==0">确 定
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
    .salary-detail-table td, .salary-detail-table th {
        font-size: 12px !important;
        height: 30px !important;
    }

    .salary-detail-table .cell {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }
</style>
<script>

    export default {
        data() {
            return {
                salary: {id: 0, name: '', total_person: 0, send_count: 0, unsend_count: 0, feedback_count: 0},
                salaryDetail: {},
                data: [],
                selectedSalary: [],
                searching: false,
                replyDialogVisible: false,
                replyContent: '',
                replyLoading: false,
                salary_password: '',
                passwordAuthDialogVisible: false,
                passwordAuthLoading: false,
                sendingAll: false,
                sendingSelected: false,

                tplCols: {},
                searchParams: {
                    feedback: '-1',
                    send: '-1',
                    keyword: '',
                    department_id: 0,
                    // 分页
                    page: 1,
                    page_size: 50,
                    paginate_total: 0
                },

                tree_data: [],
                defaultProps: {
                    children: 'children',
                    label: 'label'
                },
                treeLoading: false,
            }
        },
        computed: {
            isEmptyTplCols: function () {
                if (!this.tplCols) {
                    return true;
                }
                for (var t in this.tplCols)
                    return false;
                return true;
            }
        },
        methods: {
            params_change: function (val) {
                this.onSearchClick();
            },
            size_change: function (size) {
                this.searchParams.page_size = size;
                this.search();
            },
            current_change: function (page) {
                this.searchParams.page = page;
                this.search();
            },
            onSelectionChange: function (val) {
                this.selectedSalary = val;
            },
            onReplyClick: function (row) {
                this.salaryDetail = row;
                this.replyDialogVisible = true;
            },
            onReplyCancelClick: function () {
                this.replyDialogVisible = false;
                this.salaryDetail = {};
                this.replyContent = '';
            },
            onReplyOkClick: function () {
                let self = this;
                var content = this.replyContent.trim();
                if (content.length == 0) {
                    self.$message({
                        title: '提示',
                        message: '请输入回复内容',
                        type: 'info'
                    });
                    return;
                }
                this.replyLoading = true;
                self.$http.post('/salary/detail/reply', {
                    salary_id: self.searchParams.salary_id,
                    id: self.salaryDetail.id,
                    reply_content: content
                }).then(function (res) {
                    var data = res.data;
                    if (data.code == 0) {
                        self.$message({
                            title: '提示',
                            message: '发放成功',
                            type: 'success'
                        });
                        self.replyDialogVisible = false;
                        self.salaryDetail = {};
                        self.search();
                    } else {
                        self.$message({
                            title: '提示',
                            message: data.msg,
                            type: 'warning'
                        });
                    }
                    self.replyLoading = false;
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
                        self.search();
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
            onSendClick: function () {
                var ids = [];
                for (var i = 0; i < this.selectedSalary.length; i++) {
                    ids.push(this.selectedSalary[i].id);
                }
                let self = this;
                this.$confirm('是否要对选择人员发送工资条？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'info'
                }).then(function () {
                    self.sendingSelected = true;
                    self.$http.post('/salary/detail/send', {
                        salary_id: self.searchParams.salary_id,
                        ids: ids.join(',')
                    }).then(function (res) {
                        var data = res.data;
                        if (data.code == 0) {
                            self.$message({
                                title: '提示',
                                message: '发放成功',
                                type: 'success'
                            });
                            self.search();
                            self.get_salary();
                        } else {
                            self.$message({
                                title: '提示',
                                message: data.msg,
                                type: 'warning'
                            });
                        }
                        self.sendingSelected = false;
                    })
                }).catch(function () {

                })
            },
            onSendAllClick: function () {
                let self = this;
                this.$confirm('是否要对全部人员发送工资条？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'info'
                }).then(function () {
                    self.sendingAll = true;
                    self.$http.post('/salary/detail/send', {
                        salary_id: self.searchParams.salary_id,
                        ids: '-1'
                    }).then(function (res) {
                        var data = res.data;
                        if (data.code == 0) {
                            self.$message({
                                title: '提示',
                                message: '发放成功',
                                type: 'success'
                            });
                            self.search();
                            self.get_salary();
                        } else {
                            self.$message({
                                title: '提示',
                                message: data.msg,
                                type: 'warning'
                            });
                        }
                        self.sendingAll = false;
                    })
                }).catch(function () {

                })
            },
            onDeleteClick(){
                var ids = [];
                for (var i = 0; i < this.selectedSalary.length; i++) {
                    ids.push(this.selectedSalary[i].id);
                }
                let self = this;
                this.$confirm('确认要删除选择的工资条吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.$http.post('/salary/detail/delete', {
                        salary_id: self.searchParams.salary_id,
                        ids: ids.join(',')
                    }).then(function (res) {
                        var data = res.data;
                        if (data.code == 0) {
                            self.$message({
                                title: '提示',
                                message: '发放成功',
                                type: 'success'
                            });
                            self.search();
                            self.get_salary();
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
                this.$http.get("/salary/detail/list", {
                    params: {
                        page_size: this.searchParams.page_size,
                        page: this.searchParams.page,
                        salary_id: this.searchParams.salary_id,
                        keyword: this.searchParams.keyword,
                        feedback: this.searchParams.feedback,
                        send: this.searchParams.send,
                        department_id: this.searchParams.department_id
                    }
                }).then(function (response) {
                    if (response.data.code == -110) {//需要密码认证
                        this.passwordAuthDialogVisible = true;
                        return;
                    }
                    this.tplCols = response.data.result.tpl;
                    this.data = response.data.result.data.data;
                    this.searchParams.paginate_total = response.data.result.data.total;
                    this.searching = false;
                }).catch(function (error) {
//                    this.$message.error("查询失败");
                    this.searching = false;
                })
            },
            handleNodeClick(data) {
                //console.log(data);
                this.searchParams.department_id = data.id;
                this.onSearchClick();
            },
            get_salary(){
                this.$http.get("/salary/get", {
                    params: {id: this.salary.id}
                }).then(function (response) {
                    if (response.data.code == 0) {
                        this.salary = response.data.result;
                    } else {
                        this.$message({
                            title: '提示',
                            message: data.msg,
                            type: 'warning'
                        });
                    }
                }).catch(function (error) {
                    this.$message.error("获取工资表失败");
                })
            },
            tree_init(){
                this.treeLoading = true;
                this.$http.get("/salary/member_tree", {
                    params: {}
                }).then(function (response) {
                    this.tree_data = response.data.result;
                    this.treeLoading = false;
                }).catch(function (error) {
                    this.$message.error("查询失败");
                    this.treeLoading = false;
                })
            },
        },
        mounted(){
            this.searchParams.salary_id = this.$route.params.salary_id;
            this.salary.id = this.searchParams.salary_id;
            this.salary.name = this.$route.params.salary_name;
            this.get_salary();
            this.search();
            this.tree_init();
        }
    }

</script>