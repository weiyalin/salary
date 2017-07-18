import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);

export default new VueRouter({
    saveScrollPosition: true,
    routes: [
        {
            name: 'index',
            path: '/',
            component: resolve =>void(require(['../components/user/index.vue'], resolve))
        },
        {
            path: '/wx/reply',
            component: resolve =>void(require(['../components/wx/reply.vue'], resolve))
        },
        {
            path: '/wx/config',
            component: resolve =>void(require(['../components/wx/config.vue'], resolve))
        },
        {
            path: '/wx/template',
            component: resolve =>void(require(['../components/wx/template.vue'], resolve))
        },
        {
            path: '/wx/menu',
            component: resolve =>void(require(['../components/wx/menu.vue'], resolve))
        },
        {
            path: '/role/list',
            component: resolve =>void(require(['../components/sys/Role.vue'], resolve))
        },
        {
            path: '/role/edit',
            component: resolve =>void(require(['../components/sys/RoleEdit.vue'], resolve))
        },
        {
            path: '/user/users',
            component: resolve =>void(require(['../components/user/users.vue'], resolve))
        },
        {
            path: '/user/auth',
            component: resolve =>void(require(['../components/user/auth.vue'], resolve))
        },
        {
            path: '/user/edit',
            component: resolve =>void(require(['../components/user/user_edit.vue'], resolve))
        },
        {
            path: '/role/auth',
            component: resolve =>void(require(['../components/user/auth.vue'], resolve))
        },
        {
            path: '/user/login',
            component: resolve =>void(require(['../components/user/login.vue'], resolve))
        },
        {
            path: '/user/password',
            component: resolve =>void(require(['../components/user/password.vue'], resolve))
        },
        {
            path: '/feedback/list',
            component: resolve =>void(require(['../components/feedback/List.vue'], resolve))
        },
        {
            path: '/feedback/logViewer',
            component: resolve =>void(require(['../components/feedback/logViewer.vue'], resolve))
        },
        {
            path: '/feedback/detail/:id',
            name: 'feedback_detail',
            component: resolve =>void(require(['../components/feedback/Detail.vue'], resolve))
        },
        {
            path: '/form/list',
            component: resolve =>void(require(['../components/form/List.vue'], resolve))
        },
        {
            path: '/form/publish',
            component: resolve =>void(require(['../components/form/Publish.vue'], resolve))
        },
        {
            path: '/form/release',
            component: resolve =>void(require(['../components/form/Release.vue'], resolve))
        },
        {
            name: 'form_edit',
            path: '/form/edit',
            component: resolve =>void(require(['../components/form/Edit.vue'], resolve))
        },
        {
            name: 'stat_form',
            path: '/stat/form',
            component: resolve =>void(require(['../components/stat/Form.vue'], resolve))
        },
        {
            name: 'salary_template',
            path: '/salary/template',
            component: resolve =>void(require(['../components/salary/template.vue'], resolve))
        },
        {
            name: 'salary_import',
            path: '/salary/import',
            component: resolve =>void(require(['../components/salary/import.vue'], resolve))
        },
        {
            name: 'salary_list',
            path: '/salary/list',
            component: resolve =>void(require(['../components/salary/List.vue'], resolve))
        },
        {
            name: 'salary_detail',
            path: '/salary/detail/:salary_id/:salary_name?',
            component: resolve =>void(require(['../components/salary/Detail.vue'], resolve))
        },
        {
            name: 'salary_setting',
            path: '/salary/setting',
            component: resolve =>void(require(['../components/salary/Setting.vue'], resolve))
        },
        {
            name: 'salary_member',
            path: '/salary/member',
            component: resolve =>void(require(['../components/salary/MemberInfo.vue'], resolve))
        },
        {
            name: 'salary_feedback',
            path: '/salary/feedback',
            component: resolve =>void(require(['../components/salary/Feedback.vue'], resolve))
        },
        {
            name: 'apply_list',
            path: '/apply/list',
            component: resolve =>void(require(['../components/apply/List.vue'], resolve))
        },
    ]
})