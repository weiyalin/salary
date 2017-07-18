<template>
    <div class="page-content">

        <el-row>
            <!--<el-col :span="1">-->
                <!--<span>显示:</span>-->
            <!--</el-col>-->
            <el-col :span="3">
                <el-select v-model="status" placeholder="请选择" @change="search">
                    <el-option
                            v-for="item in options"
                            :label="item.label"
                            :value="item.value">
                    </el-option>
                </el-select>
            </el-col>
            <el-col :span="1">

                <el-button type="primary" @click="search" style="margin-left:10px;">
                    <i class="icon ion-loop"></i>
                </el-button>

            </el-col>
            <el-col :span="4">
                <el-dropdown @command="handleCommand">
                    <el-button type="primary" style="margin-left:30px;">
                        批量操作<i class="el-icon-caret-bottom el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="a">标记为已读</el-dropdown-item>
                        <el-dropdown-item command="b">标记为未读</el-dropdown-item>
                        <el-dropdown-item command="c">星标</el-dropdown-item>
                        <el-dropdown-item command="d">取消星标</el-dropdown-item>
                        <el-dropdown-item command="e">处理</el-dropdown-item>
                        <el-dropdown-item command="f">取消处理</el-dropdown-item>
                        <el-dropdown-item command="g">导出与下载</el-dropdown-item>
                        <el-dropdown-item command="h">删除反馈</el-dropdown-item>

                    </el-dropdown-menu>
                </el-dropdown>

            </el-col>
            <el-col :span="16">
                <el-tag style="margin-right:10px;">累计浏览: {{stat.view_count}} </el-tag>
                <el-tag style="margin-right:10px;">有效反馈: {{stat.submit_count}} </el-tag>
                <el-tag style="margin-right:10px;">未读反馈: {{stat.unread_count}} </el-tag>

            </el-col>
        </el-row>

        <el-table
                :data="tableData"
                border
                style="width: 100%;margin-top:10px;"
                @selection-change="handleSelectionChange">
            <div slot="empty">
                <i class="el-icon-edit"></i>
                <p>尚无反馈</p>
            </div>
            <el-table-column
                    type="selection"
                    width="55">
            </el-table-column>
            <el-table-column
                    type="index"
                    width="55">
            </el-table-column>
            <el-table-column
                    label="提交日期"
                    width="200">
                <template scope="scope">{{ parseInt(scope.row.create_time) > 0 ? new Date(parseInt(scope.row.create_time)).format('yyyy-MM-dd hh:mm:ss') : '' }}</template>
            </el-table-column>

            <el-table-column
                    prop="location"
                    label="提交地点">
            </el-table-column>
            <el-table-column
                    fixed="right"
                    label="操作"
                    width="120">
                <template scope="scope">
                    <span @click="star_change(scope.row)" class="form_icon">
                        <icon v-if="parseInt(scope.row.is_star)" name="star_on" scale="2" ></icon>
                        <icon v-else name="star_off" scale="2" ></icon>
                    </span>
                    <span @click="process_change(scope.row)" class="form_icon">
                        <icon v-if="parseInt(scope.row.is_process)" name="check_on" scale="2"  ></icon>
                        <icon v-else name="check_off" scale="2" ></icon>
                    </span>
                    <span @click="detail(scope.row)" class="form_icon">
                        <icon name="menu" scale="2" style="margin-left:10px;" ></icon>
                    </span>

                </template>
            </el-table-column>

        </el-table>
        <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="pagination.current"
                :page-sizes="[10, 20, 50, 100]"
                :page-size="pagination.pagesize"
                layout="total, sizes, prev, pager, next, jumper"
                :total="pagination.total">
        </el-pagination>

        <el-dialog title="" v-model="dialogFormVisible">
            <DetailComponent :detail-id="detail_id"/>
            <!--<div slot="footer" class="dialog-footer">-->
                <!--<el-button @click="dialogFormVisible = false">取 消</el-button>-->
                <!--<el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>-->
            <!--</div>-->
        </el-dialog>
    </div>
</template>
<style scoped>
    .page-content {
        position: relative;
        padding: 1rem;
    }

    .form_icon:hover{
        cursor:pointer;
        /*color:#ffb7b7;*/
    }

</style>
<script>
    import Form from './data/form';
    import DetailComponent from './Detail.vue';
    export default{
        data(){
            return{
                op:{
                    star_name:'star_off',
                    process_name:'check_off'
                },
                stat:{
                    view_count:0,
                    submit_count:0,
                    unread_count:0
                },
                status:0,
                search_loading:false,
                pagination: {
                    current: 1,
                    total: 0,
                    pagesize: 10
                },
                options: [{
                    value: 0,
                    label: '全部反馈'
                }, {
                    value: 1,
                    label: '已读反馈'
                }, {
                    value: 2,
                    label:'未读反馈'
                }, {
                    value: 3,
                    label: '星标反馈'
                }, {
                    value: 4,
                    label: '已处理反馈'
                }, {
                    value: 5,
                    label: '未处理反馈'
                }],
                multipleSelection: [],
                tableData: [],
                dialogFormVisible: false,
                detail_id:0,
            }
        },
        props:{
            formId:Number,

        },
        methods:{
            search(){
                var self = this;
                self.search_loading=true;
                var params = {
                    page: self.pagination.current,
                    per_page: self.pagination.pagesize,
                    status:self.status,
                    form_id:self.formId
                };

                var form = new Form();

                form.search(params, (result,error)=>{
                    if(result){
                        self.tableData = result.data;
                        self.pagination.total = result.total;

                        self.search_loading=false;
                    }
                    else {
                        console.log(error);
                        self.search_loading=false;
                    }

                });

                var p = {
                    form_id:self.formId
                };
                form.count(p, (result,error)=>{
                    if(result){
                        if(result['code']==0){
                            var stat = result['result'];
                            self.stat = stat;
                        }
                        else {
                            self.$message.error(result['msg']);
                        }

                    }
                    else {
                        console.log(error);
                    }

                });
            },
            star_change(row){
                var self = this;
                var status = Number.parseInt(row.is_star) ? 0 : 1;
                var id_list = [row.id];

                var form = new Form();
                form.setStar(id_list,status,(msg,error)=>{
                    if(msg){
                        self.search();
                    }
                    else {
                        self.$message.error(error);
                    }
                })
            },
            process_change(row){
                var self = this;
                var status = Number.parseInt(row.is_process) ? 0 : 1;
                var id_list = [row.id];
                console.log(status);
                var form = new Form();
                form.setProcess(id_list,status,(msg,error)=>{
                    if(msg){
                        self.search();
                    }
                    else {
                        self.$message.error(error);
                    }
                })
            },
            detail(row){
                console.log('detail');
                this.detail_id = Number.parseInt(row.id);
                this.dialogFormVisible = true;
            },
            handleCommand(command) {
                var self = this;
                var id_list = Array.from(this.multipleSelection,o=>o.id);
                if(command != 'g' && id_list == false){
                    self.$message({
                        message: '请至少选择一条记录',
                        type: 'warning'
                    });
                    return;
                }
                //console.log(id_list);
                var form = new Form();
                switch (command){
                    case 'a'://标记为已读
                        form.setRead(id_list,1,(msg,error)=>{
                            if(msg){
                                self.$message({
                                    message: msg,
                                    type: 'success'
                                });
                                self.search();
                            }
                            else {
                                self.$message.error(error);
                            }
                        })
                        break;
                    case 'b'://标记为未读
                        form.setRead(id_list,0,(msg,error)=>{
                            if(msg){
                                self.$message({
                                    message: msg,
                                    type: 'success'
                                });
                                self.search();
                            }
                            else {
                                self.$message.error(error);
                            }
                        })

                        break;
                    case 'c'://星标
                        form.setStar(id_list,1,(msg,error)=>{
                            if(msg){
                                self.$message({
                                    message: msg,
                                    type: 'success'
                                });
                                self.search();
                            }
                            else {
                                self.$message.error(error);
                            }
                        })
                        break;
                    case 'd'://取消星标
                        form.setStar(id_list,0,(msg,error)=>{
                            if(msg){
                                self.$message({
                                    message: msg,
                                    type: 'success'
                                });
                                self.search();
                            }
                            else {
                                self.$message.error(error);
                            }
                        })
                        break;
                    case 'e'://处理
                        form.setProcess(id_list,1,(msg,error)=>{
                            if(msg){
                                self.$message({
                                    message: msg,
                                    type: 'success'
                                });
                                self.search();
                            }
                            else {
                                self.$message.error(error);
                            }
                        })
                        break;
                    case 'f'://取消处理
                        form.setProcess(id_list,0,(msg,error)=>{
                            if(msg){
                                self.$message({
                                    message: msg,
                                    type: 'success'
                                });
                                self.search();
                            }
                            else {
                                self.$message.error(error);
                            }
                        })
                        break;
                    case 'g'://导出
                        form.setExport(id_list,self.formId,(result,error)=>{
                            if(result){
                                self.$message({
                                    message: '导出成功',
                                    type: 'success'
                                });
                                location.href=result;
                            }
                            else {
                                self.$message.error(error);
                            }
                        })
                        break;
                    case 'h'://删除
                        this.$confirm('此操作将删除所选, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            form.setDelete(id_list,(msg,error)=>{
                                if(msg){
                                    self.$message({
                                        message: msg,
                                        type: 'success'
                                    });
                                    self.search();
                                }
                                else {
                                    self.$message.error(error);
                                }
                            })

                        }).catch(() => {});

                        break;
                }
            },

            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            handleSizeChange(val) {
                this.pagination.pagesize=val;
                this.search();
                console.log(`每页 ${val} 条`);
            },
            handleCurrentChange(val) {
                this.pagination.current = val;
                this.search();
                console.log(`当前页: ${val}`);
            },
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
        },
        mounted(){
            //console.log('tt:'+this.formId);
            this.search();
        },
        components:{
            DetailComponent
        }
    }
</script>
