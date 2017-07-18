<template>
    <div>
        <el-row>
            <!--<el-col :span="10"></el-col>-->
            <el-col :span="3" :offset="10">
                <span><b>{{form_name}}</b></span>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="16">
                <span>#{{detail_id}}</span>
            </el-col>
            <el-col :span="8">
                <span style="margin-right:5px;">{{detail_date}}</span>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">
                <el-table
                        :data="tableData"
                        stripe
                        style="width: 100%">
                    <el-table-column
                            prop="title"
                            label="标题"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            label="用户作答">
                        <template scope="scope">
                             <span>
                                 <icon :name="scope.row.type" scale="2" ></icon>
                                {{scope.row.value}}
                                </span>
                        </template>
                    </el-table-column>
                </el-table>

            </el-col>
        </el-row>

    </div>
</template>
<style scoped>

</style>
<script>
    import Form from './data/form';

    export default{
        data(){
            return{
                tableData:[],
                form_name:'',
                detail_id:0,
                detail_date:'',
            }
        },
        methods:{
            detail(id){
                var self = this;
                var params = {
                    id:id,
                };
                var form = new Form();
                form.getDetail(params, (result,error)=>{
                    if(result){
                        if(result['code']==0){
                            var form_detail = result['result'];
                            self.tableData = form_detail.submits;
                            self.form_name = form_detail.form_name;
                            self.detail_id = form_detail.id;
                            self.detail_date = form_detail.date_format;
                        }
                        else {
                            self.$message.error(result['msg']);
                        }
                    }
                    else {
                        console.log(error);
                        self.$message.error(error);
                    }

                })
            }
        },
        props:{
            detailId:Number,
        },
        mounted(){
            this.detail(this.detailId);
        },
        components:{

        }
    }
</script>
