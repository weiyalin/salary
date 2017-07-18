<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>工资条管理</el-breadcrumb-item>
                <el-breadcrumb-item>导入工资表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row>
            <el-form label-width="150px">
                <el-form-item label="发放月份">
                    <el-col :span="11">
                        <el-date-picker
                                v-model="datepicker"
                                @change="pickerChange"
                                type="month"
                                placeholder="选择月">
                        </el-date-picker>
                    </el-col>

                </el-form-item>

                <el-form-item label="工资表名称">
                    <el-col :span="8">
                        <el-input v-model="name"></el-input>
                    </el-col>
                </el-form-item>


                <el-form-item label="导入工资表">
                    <el-upload
                            class="upload-demo"
                            :multiple="false"
                            action="/salary/import_file"
                            name="files"
                            :data="csrf_token"
                            :on-preview="handlePreview"
                            :on-remove="handleRemove"
                            :on-success="handleSuccess"
                            :before-upload="uploadValid"
                            :file-list="fileList">
                        <el-button size="small" type="primary">点击上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传xls,xlsx,csv文件，且不超过5M</div>
                    </el-upload>
                </el-form-item>

                <el-form-item label="选择工资条模板">
                    <el-select v-model="template_id" placeholder="请选择">
                        <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="设置加密">
                    <el-checkbox v-model="checked" @change="check">加密</el-checkbox>
                </el-form-item>
                <el-form-item label="">
                    <el-col :span="6">
                        <el-input v-if="checked" v-model="password" placeholder="请输入密码" width="200"></el-input>
                    </el-col>

                </el-form-item>

                <el-form-item>
                    <el-button type="primary" :loading="loading" @click="importTpl">开始导入</el-button>
                </el-form-item>


            </el-form>

        </el-row>
        <el-row style="margin-top:20px;">
            <el-card class="box-card">
                <span style="color:red;">导入说明：</span><br/>
                1.导入工资表的Excel,可根据企业实际使用的工资表模版进行设置 <span
                    style="color:red;">自定义工资条模版</span>,导入的工资表明细项保持工资条模板列保持一致。<br/>
                2.导入的工资表支持单行表头，多行表头请替换为单行表头, 可实际使用在工资表样板中进行设置即可.<br/>
                3.目前可支持70列工资项明细,工资表明细列中需包含'工号'列，工号与微信企业号通讯中添加人员的账号保持一致即可<br/>
                4.导入文件支持.xls,xlsx,.csv文件格式,最大不超过8M,对含复杂公式的Excel可另存为csv文件进行导入,Excel文件菜单-另存为-保存类型选csv即可.<br/>
                5.导入不成功,可将自定义的工资条模版下载后，将数据复制到下载的模版中导入
            </el-card>

        </el-row>
        <el-dialog :title="error_title" :visible.sync="error_bool" size="large" @close="onClose">
            <p style="text-align: center;position:absolute;top:5px;left:50px;right:50px;color:red">成功导入 {{success_count}}
                条</p>
            <el-row>
                <el-col class="page-content">
                    <el-row>
                        <el-col :span="24">
                            <el-table
                                    :data="error_source"
                                    border
                                    v-loading="loading">
                                <el-table-column
                                        prop="name"
                                        label="姓名"
                                        width="110">
                                </el-table-column>
                                <el-table-column
                                        prop="code"
                                        label="工号"
                                        width="120">
                                </el-table-column>

                                <el-table-column
                                        prop="error"
                                        label="失败原因">
                                </el-table-column>
                                <el-table-column
                                        prop="content"
                                        label="内容"
                                >
                                </el-table-column>
                            </el-table>
                        </el-col>
                    </el-row>
                </el-col>

            </el-row>

            <div slot="footer" class="dialog-footer">
                <el-button @click="error_bool=false">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<style scoped>
    .box-card {
        font-size: 14px;
        line-height: 1.5rem;
    }
</style>

<script>
    import Form from './form'

    export default {

        data(){
            return {
                loading: false,
                error_id: 0,
                error_title: "导入失败列表",
                error_bool: false,
                error_source: [],
                success_count: 0,
                csrf_token: {
                    _token: Laravel.csrfToken
                },
                fileList: [],
                checked: false,
                options: [],

                yearMonth: '',
                name: '',
                file: '',
                template_id: '',
                is_password: 0,
                password: '',
                datepicker: ""
            }
        },
        methods: {
            uploadValid: function (file) {
                if (this.file.length > 0) {
                    this.$message({
                        message: '只能上传一个文件',
                        type: 'warning'
                    });
                    return false;

                } else {
                    return true;
                }
            },

            init: function () {
                var self = this;

                //初始化名称日期
                var date = new Date();
                self.datepicker = date.format("yyyy-MM");
                self.name = date.getFullYear() + '年' + (date.getMonth() + 1) + '月工资表';

                let form = new Form();
                let params = {}

                form.import_list(params, function (result, err) {
                    if (result) {
                        self.options = result;
                    }
                    else {
                        self.$message.error(err);
                    }
                })
            },
            check: function () {
                if (this.checked == false) {
                    this.password = '';
                }
            },
            handleRemove(file, fileList) {
                //console.log(file, fileList);
                this.file = '';
            },
            handlePreview(file) {
                ///console.log(file);
            },
            handleSuccess(response, file, fileList){

                if (response.code == 0) {
                    this.file = response.result;
                }
                else {
                    self.$message.error(response.result);

                }
            },
            importTpl: function () {
                let self = this;

                if (self.yearMonth.length == 0) {
                    self.$message({
                        message: '请选择月份',
                        type: 'warning'
                    });
                    return;
                }
                if (self.name.length == 0) {
                    self.$message({
                        message: '工资表名称不能为空',
                        type: 'warning'
                    });
                    return;
                }
                if (self.file.length == 0) {
                    self.$message({
                        message: '请上传工资表文件',
                        type: 'warning'
                    });
                    return;
                }
                if (self.template_id == false) {
                    self.$message({
                        message: '请选择模板',
                        type: 'warning'
                    });
                    return;
                }


                let params = {
                    yearMonth: self.yearMonth,
                    name: self.name,
                    file: self.file,
                    template_id: self.template_id,
                    password: self.password
                }

                let form = new Form();
                self.loading = true;
                form.importTpl(params, function (result, err) {
                    if (result) {
                        if (result.error) {
                            self.error_id = result.id;
                            self.error_source = result.error_data;
                            self.error_bool = result.error;
                            self.success_count = result.success_count;
                            self.error_title = "导入失败列表";
                        }
                        else {
                            self.$message({
                                message: '导入成功',
                                type: 'success'
                            });
                            //跳转到查询页面
                            location.href = "/#/salary/detail/" + result.id;
                        }
                    }
                    else {
                        self.$message.error(err);
                    }
                    self.loading = false;
                })

            },
            pickerChange: function (val) {
                console.log(val);
                this.yearMonth = val;
            },
            onClose: function () {
                location.href = "/#/salary/detail/" + this.error_id;
            }

        },
        mounted() {
            this.init();
        }

    }
</script>