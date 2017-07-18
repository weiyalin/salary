/**
 * Created by wangzhiyuan on 2017/3/4.
 */

export default class Form {

    //搜索
    search(params,callback){
        var self = this;
        Vue.http.get('stat/list',{params:params}).then(function (response) {
            var result = response.data;
            callback(result,false);

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //count
    count(params,callback){
        var self = this;
        Vue.http.get('stat/count',{params:params}).then(function (response) {
            var result = response.data;
            callback(result,false);

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //标记已读/未读
    setRead(id_list,status,callback){
        var self = this;
        Vue.http.post('stat/read',{id_list:id_list,status:status}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                callback(message,false);
            }
            else {
                callback(false,message)
            }

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //标记星标
    setStar(id_list,status,callback){
        var self = this;
        Vue.http.post('stat/star',{id_list:id_list,status:status}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                callback(message,false);
            }
            else {
                callback(false,message)
            }

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //标记处理
    setProcess(id_list,status,callback){
        var self = this;
        Vue.http.post('stat/process',{id_list:id_list,status:status}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                callback(message,false);
            }
            else {
                callback(false,message)
            }

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }


    //标记删除
    setDelete(id_list,callback){
        var self = this;
        Vue.http.post('stat/delete',{id_list:id_list}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                callback(message,false);
            }
            else {
                callback(false,message)
            }

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //导出
    setExport(id_list,form_id,callback){
        var self = this;
        Vue.http.post('stat/export',{id_list:id_list,form_id:form_id}).then(function (response) {
            var message = response.data["msg"];
            var result = response.data["result"];
            if(response.data.code == 0){
                callback(result,false);
            }
            else {
                callback(false,message)
            }

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //获取详情
    getDetail(params,callback){
        var self = this;
        Vue.http.get('stat/form_detail',{params:params}).then(function (response) {
            var result = response.data;
            callback(result,false);

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }

    //获取图表数据
    getChart(params,callback){
        var self = this;
        Vue.http.get('stat/chart',{params:params}).then(function (response) {
            var result = response.data;
            callback(result,false);

        }, function (error) {
            // handle error
            console.log(error);
            callback(false,error);
        });
    }


    setHistoryOption(history){

        var option = {
            title: {
                text: '近60天概况',
                subtext: '',
                x:'center'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                // orient: 'vertical',
                left: 'right',
                data:['反馈人数','浏览次数']
            },
            xAxis:  {
                type: 'category',
                // boundaryGap: false,
                data: history.category
            },
            yAxis: {
                type: 'value',
                min:'dataMin',
                minInterval: 1,
                axisLabel: {
                    formatter: '{value}'
                }
            },
            series: [
                {
                    name:'反馈人数',
                    type:'line',
                    data:history.submit_data,
                },
                {
                    name:'浏览次数',
                    type:'line',
                    data:history.view_data,
                }
            ]
        };

        return option;
    }

    setStatOption(stat){
        var option = {
            title : {
                text: stat.form_item_name,
                subtext: '',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            // legend: {
            //     orient: 'vertical',
            //     left: 'left',
            //     data: ['直接访问','邮件营销','联盟广告','视频广告','搜索引擎']
            // },
            series : [
                {
                    name: '第'+stat.form_item_id+'题',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:stat.stat.data,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };


        return option;
    }

    static getIndexes(){

        // var indexes = [
        //     {id:'item_1',index:1},{id:'item_2',index:2},{id:'item_3',index:3},{id:'item_4',index:4},{id:'item_5',index:5},{id:'item_6',index:6},{id:'item_7',index:7},{id:'item_8',index:8},{id:'item_9',index:9},{id:'item_10',index:10},
        //     {id:'item_11',index:11},{id:'item_12',index:12},{id:'item_13',index:13},{id:'item_14',index:14},{id:'item_15',index:15},{id:'item_16',index:16},{id:'item_17',index:17},{id:'item_18',index:18},{id:'item_19',index:19},{id:'item_20',index:20},
        //     {id:'item_21',index:21},{id:'item_22',index:22},{id:'item_23',index:23},{id:'item_24',index:24},{id:'item_25',index:25},{id:'item_26',index:26},{id:'item_27',index:27},{id:'item_28',index:28},{id:'item_29',index:29},{id:'item_30',index:30},
        //
        //
        // ];

        var indexes = [];

        for(var i=1;i<=500;i++){
            indexes.push({id:'item_'+i,index:i});
        }

        return indexes;
    }

    static getItems(){
        var items = [
            {},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},

            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},

            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},


            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},


            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},
            {display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},{display:'none'},


        ]
        return items;
    }

}