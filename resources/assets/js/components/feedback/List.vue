<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>反馈模块</el-breadcrumb-item>
                <el-breadcrumb-item>用户反馈</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="page-content">
            <el-table
                    :data="tableData"
                    border
                    style="width: 100%; margin-top:10px;">
                <el-table-column
                        label="反馈列表">
                    <template scope="scope">
                        <span style="margin-left: 10px">{{ scope.row.message }}</span>
                        <!--<router-link :to="{name:'feedback_detail',params:{id:scope.row.id}}">-->
                            <!--<span style="margin-left: 10px">{{ scope.row.message }}</span>-->
                        <!--</router-link>-->
                    </template>
                </el-table-column>
                <el-table-column
                        fixed="right"
                        label="操作"
                        width="100">
                    <template scope="scope">
                        <el-button
                                @click.native.prevent="handleClick(scope.row.id)"
                                type="text"
                                size="small">
                            详细
                        </el-button>
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
    </div>
</template>
<style scoped>

</style>
<script>

    export default{
        data(){
            return {
                tableData: [],
                page : 1,
                pageSize : 10,
                total : 0,
            }
        },
        methods: {
            handleClick(id){
                window.open('/#/feedback/detail/'+id);
                //window.location.reload();
            },
            getData(){
                var query = {page:this.page,pageSize:this.pageSize};
                this.$http.get('/feedback/get_feedback_list',{params:query}).then(function(res){
                    var result = res.data;
                    this.total = result.total;
                    this.tableData = result.data;
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


        },
        mounted(){
            this.getData();
        },
        components:{

        }
    }
</script>
