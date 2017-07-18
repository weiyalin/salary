<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>表单</el-breadcrumb-item>
                <el-breadcrumb-item>表单统计</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <el-tabs v-model="current_tab" type="card" @tab-click="handleClick">
            <el-tab-pane name="list_view">
                <span slot="label"><i class="el-icon-document"></i> 列表视图</span>
                <ListComponent :form-id="form_id"/>
            </el-tab-pane>

            <!--<el-tab-pane label="列表视图" name="list_view">-->
                <!--<ListComponent :form-id="form_id"/>-->
            <!--</el-tab-pane>-->
            <el-tab-pane name="stat_view">
                <span slot="label"><i class="el-icon-time"></i> 统计视图</span>
                <ChartComponent :form-id="form_id" :count="count"/>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<style scoped>

</style>
<script>
    import ListComponent from './List.vue'
    import ChartComponent from './Chart.vue'
    export default{
        data(){
            return{
                form_id : Number.parseInt(this.$route.query.id),
                current_tab:'list_view',
                count:0,
            }
        },
        methods: {
            handleClick(tab, event) {
                //console.log(tab, event);

                if(this.current_tab == 'stat_view'){
                    this.count++;
                }
            }

        },
        mounted(){
//            this.form_id = Number.parseInt(this.$route.query.id);
//            console.log('form_id:'+this.form_id);
        },
        components:{
            ListComponent,
            ChartComponent
        }
    }
</script>
