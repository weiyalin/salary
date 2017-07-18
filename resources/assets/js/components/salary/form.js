/**
 * Created by wangzhiyuan on 2017/6/10.
 */

export default class Form {
    getInitTpl(){
        return {c1:'工号',c2:'姓名',c3:'基本工资',c4:'岗位工资',c5:'绩效',c6:'五险一金',c7:'合计',c8:'备注',c9:'',c10:'',c11:'',c12:'',c13:'',c14:'',c15:'',c16:'',c17:'',c18:'',c19:'',c20:'',c21:'',c22:'',c23:'',c24:'',c25:'',c26:'',c27:'',c28:'',c29:'',c30:'',c31:'',c32:'',c33:'',c34:'',c35:'',c36:'',c37:'',c38:'',c39:'',c40:'',c41:'',c42:'',c43:'',c44:'',c45:'',c46:'',c47:'',c48:'',c49:'',c50:''};

    }

    template_list(params,callback){
        Vue.http.get('salary/templates',{params:params}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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

    template_save(params,callback){
        Vue.http.post('salary/template_save',params).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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

    template_del(params,callback){
        Vue.http.post('salary/template_del',params).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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


    import_list(params,callback){
        Vue.http.get('salary/options',{params:params}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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

    importTpl(params,callback){
        Vue.http.post('salary/import',params).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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

    get_setting(params,callback){
        Vue.http.get('salary/notify_get',{params:params}).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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

    save_setting(params,callback){
        Vue.http.post('salary/notify_save',params).then(function (response) {
            var message = response.data["msg"];
            if(response.data.code == 0){
                var result = response.data["result"];
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


}
