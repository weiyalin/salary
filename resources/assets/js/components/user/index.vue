<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>首页</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <el-row>
            <el-col :span="6" class="text-center">
                <router-link to="/salary/template">
                    <img src="/dist/img/template.png" height="90"/>
                    <div class="text-center" style="padding:10px 0 0;">
                        自定义工资条模板
                    </div>
                </router-link>
            </el-col>
            <el-col :span="6" class="text-center">
                <router-link to="/salary/import">
                    <img src="/dist/img/import_salary.png" height="90"/>
                    <div class="text-center" style="padding:10px 0 0;">
                        导入工资表
                    </div>
                </router-link>
            </el-col>

            <el-col :span="6" class="text-center">
                <router-link to="/salary/list">
                    <img src="/dist/img/all_salary.png" height="90"/>
                    <div class="text-center" style="padding:10px 0 0;">
                        全部工资表
                    </div>
                </router-link>
            </el-col>
            <el-col :span="6" class="text-center">
                <router-link to="/salary/member">
                    <img src="/dist/img/member.png" height="100"/>
                    <div class="text-center" style="padding:10px 0 0">
                        员工管理
                    </div>
                </router-link>
            </el-col>

        </el-row>

    </div>
</template>
<style scoped>
    .text-center {
        text-align: center;
    }
</style>
<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
