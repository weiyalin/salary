<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>员工信息</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-form :inline="true" label-position="right">
            <el-form-item label="姓名或工号">
                <el-input v-model="searchParams.keyword" placeholder="输入姓名或工号查找"></el-input>
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

            <el-form-item>
                <el-button type="primary" icon="upload" @click="onSyncClick"
                           :loading="syncLoading">同步微信通讯录
                </el-button>
            </el-form-item>

            <el-form-item>
                <el-button @click="export_excel">
                    导出
                </el-button>
            </el-form-item>
        </el-form>

        <el-row>
            <el-col :span="6">
                <el-tree :data="tree_data" :props="defaultProps" @node-click="handleNodeClick" :loading="treeLoading" highlight-current  style="margin-right:10px;min-height:300px;border-top: solid 5px rgb(209,227,229);"></el-tree>

            </el-col>
            <el-col :span="18">
                <el-table
                        :data="data"
                        @current-change="handleCurrentChange"

                        border>
                    <el-table-column
                            type="index"
                            width="50"
                            label="#">
                    </el-table-column>
                    <el-table-column
                            label="工号" prop="code">
                    </el-table-column>
                    <el-table-column
                            width="100"
                            label="姓名" prop="name">
                    </el-table-column>
                    <el-table-column
                            width="100"
                            label="关注状态" prop="status">
                        <template scope="scope">
                            <el-tag v-if="scope.row.status==1" type="success">已关注</el-tag>
                            <el-tag v-if="scope.row.status==2" type="error">已禁用</el-tag>
                            <el-tag v-if="scope.row.status==4" type="warning">未关注</el-tag>
                        </template>
                    </el-table-column>

                    <el-table-column
                            width="110"
                            align="center"
                            label="工资单密码" prop="salary_password">
                        <template scope="scope">
                            <i  v-if="scope.row.salary_password"  class="ion-ios-locked-outline"></i>
                            <span v-else></span>
                        </template>
                    </el-table-column>
                    <el-table-column
                            width="120"
                            align="center"
                            v-if="is_super"
                            label="分级管理员" prop="manage_department">
                        <template scope="scope">
                            <i  v-if="scope.row.is_grade_manager == 1"  class="ion-android-done"></i>
                            <span v-else></span>
                        </template>
                    </el-table-column>


                    <el-table-column label="操作" width="120">
                        <template scope="scope">
                            <el-tooltip content="设置分级管理员" placement="top">
                                <el-button
                                        v-if="is_super"
                                        size="small" icon="setting"
                                        @click="onSettingClick(scope.row.id)">
                                </el-button>
                            </el-tooltip>

                            <el-tooltip content="清空密码" placement="top">
                                <el-button
                                        size="small" icon="minus"
                                        @click="onClearClick(scope.row.id)">
                                </el-button>
                            </el-tooltip>
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
            </el-col>
        </el-row>
        <el-dialog title="设置分级管理员" :visible.sync="dialogFormVisible">
            <el-form>
                <el-form-item label="管理部门" >
                    <el-row>
                        <el-col :span="24">
                            <el-tree ref="manage_tree"
                                     style="margin-right:10px;min-height:200px;border-top: solid 5px rgb(209,227,229);"
                                     :data.sync="manage_tree"
                                     :props="defaultProps"
                                     node-key="id"
                                     :auto-expand-parent=true
                                     :default-expanded-keys="manage_select_list"
                                     :default-checked-keys="manage_select_list"
                                     show-checkbox
                                     :highlight-current=true>
                            </el-tree>
                        </el-col>
                    </el-row>

                </el-form-item>
                <el-form-item label="登录手机号" >
                    <el-input v-model="manage_mobile" placeholder="请输入手机号"></el-input>
                </el-form-item>
                <el-form-item label="登录密码" >
                    <el-button size="small" v-if="is_grade_manager == 1" @click="showPassword"><i class="ion-android-create"></i> 更改密码</el-button>
                    <el-input v-else v-model="manage_password" placeholder="请输入密码"></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="SaveManageClick">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>

    export default {
        data() {
            return {

                options:[],
                data: [],
                searching: false,
                syncLoading: false,
                treeLoading:false,
                searchParams: {
                    keyword: '',
                    status:0,
                    department_id:0,
                    // 分页
                    page: 1,
                    page_size: 10,
                    paginate_total: 0
                },

                tree_data: [],
                defaultProps: {
                    children: 'children',
                    label: 'label'
                },

                dialogFormVisible: false,
                manage_tree:[],
                manage_select_list:[],
//                manage_values:[],
//                manage_data:[],
                manage_id:0,
                manage_password:'',
                manage_mobile:'',
                is_super:0,
                is_grade_manager:0,

            }
        },
        methods: {
            showPassword: function(){
                this.is_grade_manager = this.is_grade_manager == 1 ? 0 : 1;
            },
            size_change: function (size) {
                this.searchParams.page_size = size;
                this.search();
            },
            current_change: function (page) {
                this.searchParams.page = page;
                this.search();
            },
            onSettingClick(id){
                var self = this;
                self.manage_id = id;
                self.$http.get("/salary/member_get_manage", {params:{id:id}
                }).then(function (response) {
                    if(response.data.code == 0){
                        self.manage_tree = response.data.result.tree;
                        self.manage_select_list = response.data.result.select_list;
                        self.is_grade_manager = response.data.result.is_grade_manager;
                        self.manage_mobile = response.data.result.mobile;
                        self.dialogFormVisible=true;

                    }
                    else {
                        self.$message({
                            title: '提示',
                            message: response.data.msg,
                            type: 'warning'
                        });
                    }
                }).catch(function (error) {
                    console.log(error);
                    self.$message.error(error);
                })
            },
            SaveManageClick(){
                console.log('save...');
                let self = this;
                let checkedNodes = self.$refs['manage_tree'].getCheckedKeys();

                self.$http.post('/salary/member_save_manage', {id: self.manage_id,values:checkedNodes,password:self.manage_password,mobile:self.manage_mobile}).then(function (res) {
                    var data = res.data;
                    if (data.code == 0) {
                        self.dialogFormVisible=false;
                        self.manage_id = 0;
                        self.manage_values = [];
                        self.manage_data = [];
                        self.manage_password = '';
                        self.manage_mobile = '';
                        self.$message({
                            title: '提示',
                            message: data.msg,
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
            },

            onClearClick(id){
                let self = this;
                this.$confirm('确认清空密码吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.$http.post('/salary/member_clear', {id: id}).then(function (res) {
                        var data = res.data;
                        if (data.code == 0) {
                            self.$message({
                                title: '提示',
                                message: '清空密码成功',
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
                this.$http.get("/salary/member_search", {
                    params: {
                        page_size: this.searchParams.page_size,
                        page: this.searchParams.page,
                        keyword: this.searchParams.keyword,
                        status: this.searchParams.status,
                        department:this.searchParams.department_id
                    }
                }).then(function (response) {
                    this.data = response.data.result.table.data;
                    this.searchParams.paginate_total = response.data.result.table.total;
                    this.is_super = response.data.result.is_super;
                    this.searching = false;
                }).catch(function (error) {
                    this.$message.error("查询失败");
                    this.searching = false;
                })
            },
            handleNodeClick(data) {
                //console.log(data);
                this.searchParams.department_id = data.id;
                this.search();
            },
            onSyncClick(){
                var self = this;
                self.syncLoading = true;
                self.$http.get("/salary/member_sync", {params:{}
                }).then(function (response) {
                    if(response.data.code == 0){
                        self.$message({
                            title: '提示',
                            message: '同步完成',
                            type: 'success'
                        });
                        this.onOptions();
                        this.tree_init();
                    }
                    self.syncLoading = false;
                }).catch(function (error) {
                    console.log(error);
                    self.$message.error(error);
                    self.syncLoading = false;
                })
            },

            onOptions(){
                var self = this;
                //self.syncLoading = true;
                self.$http.get("/salary/member_options", {params:{}
                }).then(function (response) {
                    if(response.data.code == 0){
                        self.options = response.data.result;
                        this.search();
                    }
                    //self.syncLoading = false;
                }).catch(function (error) {
                    console.log(error);
                    self.$message.error(error);
                    //self.syncLoading = false;
                })
            },
            onOptionChange(){
                this.search();
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
            handleCurrentChange(val) {
//                this.currentRow.code = val.code;
//                this.currentRow.name = val.name;
//                this.currentRow.manage = val.manage_department;
                console.log(val);
            },
            export_excel: function () {
                window.location = "/salary/member_export?" + $.param({
                    keyword: this.searchParams.keyword,
                    status: this.searchParams.status,
                    department:this.searchParams.department_id
                })
            }
        },
        mounted(){
            this.onOptions();
            this.tree_init();
        }
    }

</script>