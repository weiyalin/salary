<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>用户反馈</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-form :inline="true" label-position="right">
            <el-form-item label="反馈内容">
                <el-input v-model="searchParams.content" placeholder="输入反馈内容查找"></el-input>
            </el-form-item>
            <el-form-item label="姓名">
                <el-input v-model="searchParams.name" placeholder="输入反馈人姓名查找"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="search" @click="onSearchClick"
                           :loading="searching">查询
                </el-button>
            </el-form-item>


        </el-form>

        <el-row>
            <el-col :span="24">
                <el-table
                        :data="data"
                        border>
                    <el-table-column
                            type="index"
                            width="50"
                            label="#">
                    </el-table-column>
                    <el-table-column
                            label="反馈内容" prop="content">
                    </el-table-column>
                    <el-table-column
                            width="100"
                            label="反馈人" prop="user_name">
                    </el-table-column>
                    <el-table-column
                            width="130"
                            label="反馈时间" prop="updated_at">

                    </el-table-column>
                    <el-table-column
                            label="回复内容" prop="reply_content">
                    </el-table-column>
                    <el-table-column
                            width="100"
                            label="回复人" prop="reply_user_name">
                    </el-table-column>
                    <el-table-column
                            width="130"
                            label="回复时间" prop="reply_time">

                    </el-table-column>


                    <el-table-column label="操作" width="70">
                        <template scope="scope">

                            <el-tooltip content="回复" placement="top">
                                <el-button
                                        size="small"
                                        @click="onReplyClick(scope.row.id)">回复
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
        <el-dialog title="请输入回复内容" :visible.sync="dialogFormVisible">
            <el-row>
                <el-input
                        type="textarea"
                        :rows="5"
                        placeholder="请输入内容"
                        v-model="current_content">
                </el-input>


            </el-row>
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
                data: [],
                searching: false,
                searchParams: {
                    content: '',
                    name: '',

                    // 分页
                    page: 1,
                    page_size: 50,
                    paginate_total: 0
                },

                dialogFormVisible: false,
                current_content:'',
                current_id:0,

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

            SaveManageClick(){
                //console.log(this.manage_values);
                let self = this;
                self.$http.post('/salary/feedback_reply', {id: self.current_id,content:self.current_content}).then(function (res) {
                    var data = res.data;
                    if (data.code == 0) {
                        self.dialogFormVisible=false;
                        self.current_id = 0;
                        self.current_content = '';
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

            onReplyClick(id){
                this.current_id = id;
                this.current_content = '';
                this.dialogFormVisible = true;

            },
            onSearchClick(){
                this.searchParams.page = 1;
                this.search();
            },
            search(){
                this.searching = true;
                this.$http.get("/salary/feedback_lists", {
                    params: {
                        page_size: this.searchParams.page_size,
                        page: this.searchParams.page,
                        name: this.searchParams.name,
                        content: this.searchParams.content,
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


        },
        mounted(){
            this.search();
        }
    }

</script>