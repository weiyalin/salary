/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

require('./common.js');

window.APP_DEBUG = true;
// require("../library/js/bootstrap-datetimepicker.min.js")//TODO 本身大小36KB,require进来后app.js大小增加1.3MB?

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import ElementUI from 'element-ui' //TODO 2.6M
Vue.use(ElementUI);

import App from './App.vue'
// import '../element-theme/index.css' //TODO 0.4M 从js中分离,单独以css文件的形式加载,移动gulpfile.js文件中处理

/*import Icon from 'vue-svg-icon/Icon.vue';
 Vue.component('icon', Icon);
 Icon.inject('star_on');
 Icon.inject('star_off');
 Icon.inject('check_on');
 Icon.inject('check_off');
 Icon.inject('refresh');
 Icon.inject('menu');
 Icon.inject('checkbox');
 Icon.inject('number');
 Icon.inject('select');
 Icon.inject('radio');
 Icon.inject('text');
 Icon.inject('textarea');
 Icon.inject('date');
 Icon.inject('image');
 Icon.inject('file');
 Icon.inject('rank');
 Icon.inject('currency');
 Icon.inject('multi_select');
 Icon.inject('country');
 Icon.inject('tabular');*/


import * as filters from './filters/index'
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key])
});

import router from './router/index.js'              //引入路由

const app = new Vue({
    el: '#app',
    render: h => h(App),
    router
});

