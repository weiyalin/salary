<template>
    <div>
        <div id="main" style="width: 900px;height:400px;margin-top:10px;"></div>
        <!--<div v-html="rawHtml"></div>-->
        <div>
            <el-card v-for="item in indexes" class="box-card chart_top" :style="items[item.index]">
                <div :id="item.id" style="width: 900px;height:400px;"></div>
            </el-card>
        </div>

        <!--<line-chart :chart-data="lineData"></line-chart>-->

    </div>
</template>
<style scoped>
    .chart_top{
        margin-top:5px;
        display:none;
    }
</style>
<script>
    import echarts from 'echarts';
//    import { Line,Bar } from 'vue-chartjs/es'

    //import LineChart from './data/LineChart'

    import Form from './data/form';

    export default{
        data(){
            return{
                items:Form.getItems(),
                indexes:Form.getIndexes(),
                //lineData:null,
            }
        },
        methods:{
            init(){
                console.log('init');
                var self = this;
                var params = {
                    form_id:self.formId,
                };
                var form = new Form();
                var myChart = echarts.init(document.getElementById('main'));
                myChart.showLoading();
                form.getChart(params, (result,error)=>{
                    if(result){
                        if(result['code']==0){

                            var form_chart = result['result'];
                            var history = form_chart['history'];
                            var stat = form_chart['stat'];

                            //load history
                            var history_option = form.setHistoryOption(history);
                            myChart.setOption(history_option);

                            myChart.hideLoading();

//                            var count = stat.length;
//                            self.setItems(count);
                            //self.setIndexes(count);
                            //load stat
                            var i = 1;
                            for(var j in stat){
                                self.items[i].display='block';
                                myChart = echarts.init(document.getElementById('item_'+i));
                                var stat_option = form.setStatOption(stat[j]);
                                //console.log(stat_option);
                                myChart.setOption(stat_option);
                                i++;
                            }


                        }
                        else {
                            self.$message.error(result['msg']);
                            myChart.hideLoading();
                        }
                    }
                    else {
                        console.log(error);
                        self.$message.error(error);
                        myChart.hideLoading();
                    }

                })

            },
//            setIndexes(count){
//                this.indexes = [];
//                for(var i = 1; i++; i<=count){
//                    this.indexes.push({
//                        id:'item_'+i,
//                        index:i
//                    })
//                }
//
//                //this.indexes = indexes;
//            },
//
//            setItems(count){
//                var items = [];
//                for(var i = 0; i++; i<=count){
//                    items.push({
//                        display:'none'
//                    })
//                }
//
//                this.items = items;
//            },
        },
        props:{
            formId:Number,
            count:Number,
        },
        mounted(){
//            this.rawHtml = '';
//            for(var i=1;i++;i<10){
//                this.rawHtml = this.rawHtml + "<div id='item_"+i+"' style='width: 900px;height:400px;'></div>";
//            }
        },
        watch:{
            count: function(val,oldVal){
                console.log('new: %s, old: %s', val, oldVal);
                this.init();
            }

        },
        components:{
            //LineChart,
        }
    }
</script>
