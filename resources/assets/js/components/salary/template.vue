<template>
    <div>
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>工资条管理</el-breadcrumb-item>
                <el-breadcrumb-item>自定义模板</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row>
            <el-col>
                <el-card class="box-card" v-for="template in templates" style="margin-top:20px;">
                    <div slot="header" class="clearfix">
                        <Tpltable :template="template" @on-data-change="onDataChange"></Tpltable>
                    </div>
                    <div class="item" style="position:relative;">

                        <span style="position:absolute;left:0;top:-10px;font-size:12px;">{{template.name}}</span>

                            <el-form :inline="true" label-position="right" style="position:absolute;right:0;top:-18px;">
                                <el-form-item>
                                    <el-upload
                                            class="avatar-uploader"
                                            :show-file-list="false"
                                            :multiple="false"
                                            :action="template.url"
                                            name="files"
                                            :data="csrf_token"
                                            :on-success="handleSuccess">
                                        <el-button type="text" size="mini">导入工资条模板</el-button>
                                    </el-upload>
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="text" @click="download(template.id)" size="mini">下载此模板</el-button>
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="text" @click="del(template.id)" size="mini">删除此模板</el-button>
                                </el-form-item>
                            </el-form>
                            <!--<el-button type="text" @click="importTpl(template.id)">导入工资条模板</el-button>-->
                    </div>

                </el-card>
            </el-col>
        </el-row>
        <el-row style="margin-top:20px;">
            <el-card class="box-card">
                <div slot="header" class="clearfix" style="font-size: 14px;line-height: 1.5rem;">
                    <p style="color:green">
                        根据企业实际使用工资条编辑发放明细项后保存模版,导入工资表的发放明细项需和这里定义的工资条模版保持一致,编辑时可类似excel一样编辑拖动各列位置<br/>
                        工资条明细项中需包含'工号'列 导入工资表中工号和微信企业号中员工通讯录中的员工账号保持一致即可<br/>
                        工资条显示设置:可设置工资明细项为0 或者为空时,是否在微信端展示其工资项，默认工资表中项在微信端全部展示

                    </p>
                    <p style="color:red;">可直接从企业已使用的工资表中直接导入工资条模版</p>
                </div>
                <div class="item" style="text-align:center;">
                    <el-button type="primary" @click="saveTpl">保存模板</el-button>
                    <el-button type="success" @click="newTpl">新建工资条模板</el-button>

                </div>
            </el-card>

        </el-row>
        <el-dialog title="新建工资条模板" :visible.sync="dialogFormVisible" size="tiny">
            <el-form>
                <el-form-item label="模板名称" label-width="80">
                    <el-input v-model="template_name"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="createTpl">确 定</el-button>
            </div>
        </el-dialog>
    </div>


</template>
<style scoped>
    .box-card{
        font-size:14px;
    }
</style>

<script>
    import Tpltable from './Table.vue'
    import Form from './form'

    export default {
        components: {
            Tpltable
        },
        data(){
//            let form = new Form();
//            let tpl = form.getInitTpl();

            return {
                csrf_token: {
                    _token: Laravel.csrfToken,
                },

                templates: [],//模板列表
                dic_list: [],//更改变化的模板

                dialogFormVisible: false,
                template_name: '',
                file: ''
            }
        },
        methods: {

            handleSuccess(response, file, fileList){
                var self = this;
                if (response.code == 0) {
                    self.file = response.result;
                    self.$message({
                        message: '上传成功',
                        type: 'success'
                    });
                    self.init();
                }
                else {
                    self.$message({
                        message: response.msg,
                        type: 'warning'
                    });
                    //self.$message.error(response.result);

                }
            },
            onDataChange: function (obj) {
                let self = this;
                console.log('change..');
                console.log(obj);

                this.dic_list[obj.id] = obj;

            },

            saveTpl: function () {
                let self = this;
                let form = new Form();
                if (self.dic_list.length > 0) {
                    let list = [];
                    self.dic_list.forEach(function (item) {
                        if (item) {
                            list.push(item);
                        }
                    })
                    let params = {
                        templates: list
                    }

                    form.template_save(params, function (result, err) {
                        if (result) {
                            self.$message({
                                message: result,
                                type: 'success'
                            });
                            self.init();
                        }
                        else {
                            self.$message.error(err);
                        }
                    })

                }
                else {
                    self.$message({
                        message: '没有更改的模板',
                        type: 'warning'
                    });
                }
            },
            createTpl: function () {
                let form = new Form();
                let tpl = form.getInitTpl();
                let template = {
                    id: 0,
                    name: this.template_name,
                    data: [tpl]
                };
                this.dic_list[0] = template;
                this.saveTpl();
                this.dialogFormVisible = false;
            },
            newTpl: function () {
                this.dialogFormVisible = true;
            },

            init: function () {
                let self = this;
                self.templates = [];
                self.dic_list = [];
                let form = new Form();
                let params = {}

                form.template_list(params, function (result, err) {
                    if (result) {
                        self.templates = result;
                    }
                    else {
                        self.$message.error(err);
                    }
                })


            },
            importTpl: function (id) {

            },
            download: function (id) {
                let self = this;
                if (id == 0) {
                    self.$message({
                        message: '请保存后下载',
                        type: 'warning'
                    });

                }
                else {
                    window.location.href = "salary/template_download?id=" + id
                }
            },
            del: function (id) {
                let self = this;
                this.$confirm('确认删除吗？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    let form = new Form();
                    let params = {
                        id: id
                    };
                    form.template_del(params, function (result, err) {
                        if (result) {
                            self.$message({
                                message: result,
                                type: 'success'
                            });
                            self.init();
                        }
                        else {
                            self.$message.error(err);
                        }
                    })

                }).catch(function () {
//                    self.$message({
//                        title: '提示',
//                        message: '删除失败,请重试',
//                        type: 'warning'
//                    });
                })


            }
        },
        mounted() {
            this.init();
        }


    }
</script>