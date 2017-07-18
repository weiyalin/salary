<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>用户模块</el-breadcrumb-item>
                <el-breadcrumb-item :to="{path : '/user/users'}">用户管理</el-breadcrumb-item>
                <el-breadcrumb-item>权限管理</el-breadcrumb-item>
            </el-breadcrumb>
         </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
