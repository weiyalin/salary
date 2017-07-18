String.prototype.trim = function () {
    return this.replace(/(^\s*)|(\s*$)/g, '');
};
String.prototype.ltrim = function () {
    return this.replace(/(^\s*)/g, '');
};
String.prototype.rtrim = function () {
    return this.replace(/(\s*$)/g, '');
};

String.prototype.get_number = function(){
    var v= this;
    var s = '';
    for(var i=v.length-1;i>=0;i--)
    {
        if("0123456789".indexOf(v.substr(i,1))>-1){
            s = v.substr(i,1) + s;
        }
        else {
            break;
        }
    }

    return parseInt(s);
};

Date.prototype.format = function(format){
    var o = {
        "M+" : this.getMonth()+1, //month
        "d+" : this.getDate(), //day
        "h+" : this.getHours(), //hour
        "m+" : this.getMinutes(), //minute
        "s+" : this.getSeconds(), //second
        "q+" : Math.floor((this.getMonth()+3)/3), //quarter
        "S" : this.getMilliseconds() //millisecond
    }

    if(/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }

    for(var k in o) {
        if(new RegExp("("+ k +")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
        }
    }
    return format;
}