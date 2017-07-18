<template>
    <div style="overflow: hidden;">
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>表单</el-breadcrumb-item>
                <el-breadcrumb-item>表单列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-button style="margin-bottom: 1rem;" @click="show_form">创建表单</el-button>
        <el-select clearable v-model="group_id" placeholder="筛选分组">
            <el-option
                v-for="item in form_group"
                :label="item.name"
                :value="item.id">
            </el-option>
        </el-select>
        <el-button style="margin-bottom: 1rem;" @click="show_group_list = true">分组操作</el-button>
        <el-table :data="form_list" v-loading="loading">
            <el-table-column width="240" label="表单名称">
                <template scope="scope">
                    <i class="ion-arrow-up-c" @click="show_form_group(scope.row)" v-if="scope.row.is_top > 0" style="cursor: pointer;"></i>
                    <i @click="show_form_group(scope.row)" v-if="scope.row.is_follow > 0" class="ion-android-star" style="cursor: pointer;"></i>
                    <span v-if="scope.row.form_name.length > 10">
                        {{ scope.row.form_name.substr(0, 10) + "..." }}
                    </span>
                    <span v-else>
                        {{ scope.row.form_name }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="group_name" label="分组" sortable></el-table-column>
            <el-table-column width="120" prop="feedback_count" label="反馈人数" sortable></el-table-column>
            <el-table-column prop="status" label="状态" sortable></el-table-column>
            <el-table-column width="220" prop="create_time" label="创建时间" sortable></el-table-column>
            <el-table-column label="操作" width="300">
                <template scope="scope">
                    <router-link :to="{name: 'form_edit', query: {id: scope.row.id}}">
                        <el-button size="small"><i class="ion-ios-compose-outline" style="color: #2D8FD9;"></i> 编辑</el-button>
                    </router-link>
                    <router-link :to="{name: 'stat_form', query: {id: scope.row.id}}">
                        <el-button size="small"><i class="ion-android-apps" style="color: #2D8FD9;"></i> 详情</el-button>
                    </router-link>
                    <el-button size="small" @click="show_form_group(scope.row)"><i class="ion-android-folder-open" style="color: #2D8FD9;"></i> 分组</el-button>
                    <el-button size="small" @click="show_preview(scope.row)"><i class="ion-android-open" style="color: #2D8FD9;"></i> 预览</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
            style="padding: 1rem 0;"
            @current-change="get_list"
            @size-change="size_change"
            :page-sizes="[10, 20, 50]"
            :page-size="page_size"
            layout="total, sizes, prev, pager, next, jumper"
            :total="paginate_total">
        </el-pagination>

        <!-- 创建表单 -->
        <el-dialog title="创建表单" v-if="visible" v-model="visible" size="small">
            <el-form label-width="6em" @submit.prevent.native="create_form">
                <el-form-item label="名称">
                    <el-input id="create_form_input" v-model="form.name" autofocus></el-input>
                </el-form-item>
                <el-form-item>
                    <el-checkbox v-model="form.is_top">置顶</el-checkbox>
                    <el-checkbox v-model="form.is_follow">关注</el-checkbox>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="create_form" :loading="create_loading">确定</el-button>
                    <el-button @click="visible = false">取消</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>

        <!-- 预览表单 -->
        <el-dialog title="预览" v-if="preview" v-model="preview">
            <el-input :value="preview_url" readonly>
                <el-button slot="append" @click="open_preview_url">打开</el-button>
            </el-input>
            <p style="margin: 1em 0;">预览表单仅供预览，不能提交数据。</p>
            <iframe id="preview_box" style="width: 100%;" frameborder="0" :src="preview_url + '?from=preview'"></iframe>
            <router-link :to="{name: 'form_edit', query: {id: preview_form.id}}">
                <el-button type="primary"><i class="ion-ios-compose-outline"></i> 编辑表单</el-button>
            </router-link>
            <el-button :loading="save_template_loading" @click="save_template">保存为模版</el-button>
        </el-dialog>

        <!-- 分组 添加 列表操作 -->
        <el-dialog title="分组" v-if="show_group_list" v-model="show_group_list" size="tiny">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <p v-if="form_group.length > 0">单击分组名称可编辑分组，回车保存分组</p>
                    <p v-else>尚未创建分组</p>
                </div>
                <el-row v-if="form_group.length > 0" v-for="item in form_group" style="margin-top: 1em" v-loading="change_group_loading[item.id]">
                    <el-col :span="19">
                        <div style="padding: 8px;" v-if="show_group_edit != item.id" @click="show_group_edit = item.id">
                            {{ item.name }}
                        </div>
                        <el-input v-else v-model="item.name" @keydown.enter.native="edit_group(item.id, item.name)" autofocus></el-input>
                    </el-col>
                    <el-col :span="4" :offset="1">
                        <el-button @click="delete_group(item.id)">删除</el-button>
                    </el-col>
                </el-row>
            </el-card>
            <el-form :inline="true" style="margin-top: 1em" @submit.prevent.native="create_group" v-loading="create_group_loading">
                <el-row>
                    <el-col :span="19">
                        <el-input v-model="create_group_name" placeholder="分组名称" autofocus></el-input>
                    </el-col>
                    <el-col :span="4" :offset="1">
                        <el-button type="primary" @click="create_group">创建</el-button>
                    </el-col>
                </el-row>
            </el-form>
        </el-dialog>

        <!-- 编辑表单分组 -->
        <el-dialog title="编辑表单" v-if="show_group_modal" v-model="show_group_modal" size="small">
            <el-form label-width="6em" @submit.prevent.native="edit_form_group">
                <el-form-item label="名称">
                    <el-input v-model="show_group_form.form_name" autofocus></el-input>
                </el-form-item>
                <el-form-item label="分组">
                    <el-select clearable v-model="show_group_form.group_id" placeholder="选择分组">
                        <el-option
                            label="未分类"
                            :value="0">
                        </el-option>
                        <el-option
                            v-for="item in form_group"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-checkbox v-model="show_group_form.is_top">置顶</el-checkbox>
                    <el-checkbox v-model="show_group_form.is_follow">关注</el-checkbox>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="edit_form_group" :loading="edit_form_group_loading">确定</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
    import {format_time} from '../../filters/index'
    export default {
        data: function () {
            return {
                loading: true,
                form_list: [],
                form_group: [],
                status: ["未发布", "发布中", "停止发布"],
                visible: false, // 创建表单框是否可见
                form: {
                    name: "新的表单",
                    is_top: "",
                    is_follow: ""
                },
                group_id: false,
                create_loading: false,
                page_size: 10,
                paginate_total: 0,
                preview: false,
                preview_form: false,
                preview_url: "",
                save_template_loading: false,

                show_group_list: false,
                // 创建分组 Loading 效果
                create_group_loading: false,
                // 创建分组的名称
                create_group_name: "",
                // 展示编辑框的索引
                show_group_edit: 0,
                // 编辑分组名称的 Loading 效果
                change_group_loading: {},
                // 是否展示编辑表单分组的 Modal
                show_group_modal: false,
                show_group_form: {},
                edit_form_group_loading: false
            }
        },
        watch: {
            group_id: function (now) {
                this.page = 1
                this.get_list()
            }
        },
        methods: {
            get_group_list: function () {
                let self = this
                this.$http.get("/form/group", {params: {
                    group_id: this.group_id
                }}).then(function (response) {
                    self.form_group = response.body.form_group
                }).catch(function (error) {
                    self.$message.error("加载分组失败")
                })
            },
            get_list: function (page = 1) {
                if (typeof page != 'number') {
                    page = 1
                }
                this.loading = true
                this.$http.get("/form", {params: {
                    page: page,
                    limit: this.page_size,
                    group_id: this.group_id
                }}).then((response) => {
                    this.loading = false
                    let form_list = response.body.forms.data
                    for (let i = 0; i < form_list.length; i++) {
                        form_list[i].group_name = this.get_group_name(form_list[i].group_id)
                        form_list[i].status = this.status[form_list[i]["status"]]
                        form_list[i].create_time = format_time(form_list[i]["create_time"] / 1000)
                    }
                    this.form_list = form_list
                    this.paginate_total = response.body.forms.total
                })
            },
            create_form: function () {
                this.create_loading = true
                this.$http.post("/form/create", {
                    form: this.form
                }).then((response) => {
                    this.create_loading = false
                    if (response.body.success) {
                        this.$router.push({name: "form_edit", query: {
                            id: response.body.form.id
                        }})
                    } else {
                        this.$message(response.body.message)
                    }
                }).catch((error) => {
                    this.create_loading = false
                    this.$message.error("创建表单失败")
                })
            },
            show_form: function (event) {
                this.visible = true
                this.$nextTick(function () {
                    $("#create_form_input input").focus()
                })
            },
            size_change: function (size) {
                this.page_size = size
                this.get_list()
            },
            show_preview: function (form) {
                this.preview = true
                this.preview_form = form
                this.preview_url = `http://${window.location.host}/submit/${form.url_code}`
                this.$nextTick(function () {
                    $("#preview_box").height($(window).height() / 2.4)
                })
            },
            open_preview_url: function () {
                window.open(this.preview_url)
            },
            save_template: function () {
                let form = this.preview_form,
                    self = this
                this.template_save_loading = true
                this.$http.post("/form/template/create", {
                    id: form.id,
                    name: form.form_name
                }).then(function (response) {
                    self.template_save_loading = false
                    self.$message(response.data.message)
                }).catch(function (error) {
                    self.template_save_loading = false
                    self.$message.error("网络错误")
                })
            },
            delete_group: function (id) {
                let self = this
                this.$set(this.change_group_loading, id, true)
                this.$http.post("/form/group/delete", {
                    id: id
                }).then(function (response) {
                    self.$set(self.change_group_loading, id, false)
                    self.$message(response.data.message)
                    self.get_group_list()
                }).catch(function (error) {
                    self.$set(self.change_group_loading, id, false)
                    self.$message.error("删除失败")
                })
            },
            create_group: function () {
                if (!this.create_group_name) {
                    this.$message("请输入分组名称")
                } else {
                    let self = this
                    this.create_group_loading = true
                    this.$http.post("/form/group/create", {
                        name: this.create_group_name
                    }).then(function (response) {
                        self.create_group_loading = false
                        self.get_group_list()
                        self.create_group_name = ""
                        self.$message(response.data.message)
                    }).catch(function (error) {
                        self.create_group_loading = false
                        self.$message.error("网络错误")
                        self.get_group_list()
                    })
                }
            },
            get_group_name: function (id) {
                let form_group = this.form_group
                for (let i = 0; i < form_group.length; i++) {
                    if (form_group[i].id == id) {
                        return form_group[i].name
                    }
                }

                return "未分类"
            },
            edit_group: function (id, name) {
                let self = this
                this.$set(this.change_group_loading, id, true)
                this.$http.post("/form/group/edit", {
                    id: id,
                    name: name
                }).then(function (response) {
                    self.$set(self.change_group_loading, id, false)
                    if (!response.data.success) {
                        self.$message(response.data.message)
                    }
                    self.show_group_edit = 0
                }).catch(function (error) {
                    self.$set(self.change_group_loading, id, false)
                    self.$message("网络错误")
                    self.show_group_edit = 0
                })
            },
            show_form_group: function (form) {
                this.show_group_modal = true
                this.show_group_form = {
                    id: form.id,
                    form_name: form.form_name,
                    group_id: parseInt(form.group_id),
                    is_top: form.is_top > 0 ? true : false,
                    is_follow: form.is_follow ? true : false
                }
            },
            edit_form_group: function () {
                let show_group_form = this.show_group_form,
                    self = this
                
                this.edit_form_group_loading = true
                this.$http.post("/form/edit", {
                    id: show_group_form.id,
                    form: show_group_form
                }).then(function (response) {
                    self.show_group_modal = false
                    self.edit_form_group_loading = false
                    self.$message("编辑成功")
                    self.get_list()
                }).catch(function (error) {
                    this.$message.error("网络错误")
                    self.edit_form_group_loading = false
                })
            }
        },
        mounted: function () {
            this.get_group_list()
            this.get_list()
        }
    }
</script>