<template>
    <div>
        <remote-link href="/css/alpaca.css"></remote-link>
        <remote-link href="/css/bootstrap.min.css"></remote-link>
        <remote-link href="/css/bootstrap-datetimepicker.css"></remote-link>

        <remote-script src="/js/bootstrap.min.js"></remote-script>
        <remote-script src="/js/handlebars.min.js"></remote-script>
        <remote-script src="/js/jquery.price_format.min.js"></remote-script>
        <remote-script src="/js/moment-with-locales.min.js"></remote-script>
        <remote-script src="/js/bootstrap-datetimepicker.min.js"></remote-script>
        <remote-script src="/js/alpaca.js"></remote-script>

        <!-- 面包屑导航 -->
        <div class="gm-breadcrumb">
            <i class="ion-ios-home gm-home"></i>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    表单
                </el-breadcrumb-item>
                <el-breadcrumb-item>
                    编辑表单
                </el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <!-- 步骤条 -->
        <el-steps :space="140" :active="1" :center="true" style="margin-top:-73px;display:list-item;">
            <el-step class="to-edit step_edit" title="编辑" icon="edit"></el-step>
            <el-step class="step_upload" title="发布" icon="upload"></el-step>
        </el-steps>

        <!-- 编辑区域 -->
        <el-row v-loading="loading" id="editable_box" :gutter="8" style="margin-top: 1em">

            <!-- 左侧组件栏 -->
            <el-col :span="6" style="padding: 1em;border: 1px solid #D9DFE2;">
                <div style="margin-bottom: 1em;height: 40px;line-height: 40px;padding-left: 15px;background-color: #fff;color: rgb(72, 102, 106);border-bottom: 1px solid rgb(223, 236, 236);">
                    组件
                    <i class="el-icon-setting" style="float: right;line-height: 40px;margin-right: 16px;cursor: pointer;" @click="developer_modal = true"></i>
                </div>

                <!-- 组件列表 -->
                <el-row id="widgets-box">
                    <el-col
                        v-for="item in widgets"
                        :span="12"
                        class="widget-item"
                        :data-type="item.schema.type"
                        :data-schema="JSON.stringify(item.schema)"
                        :data-option="JSON.stringify(item.option)">
                        <div class="widget-item-drop">
                            <!-- 拖拽到中间的样式 -->
                            <div class="widget-item-line"></div>
                            <span class="widget-item-text">放在这里</span>
                        </div>
                        <div class="widget-item-description">
                            <!-- 在组件列表中的样式 -->
                            <i v-if="item.icon" :class="item.icon"></i>
                            <div>{{ item.schema.title }}</div>
                        </div>
                    </el-col>
                </el-row>
            </el-col>
            <!-- 表单预览界面 -->
            <el-col :span="12">
                <div class="drop_box">
                    <el-card class="box-card">
                        <!-- 表单标题 -->
                        <div
                            @click="activeCollapse = ['2']"
                            style="text-align: -webkit-center;
                                background-color: #3b67a0;
                                padding: 12px;
                                color: #fff;
                                font-size: 25px;">
                            {{ form.form_name }}
                        </div>
                        <!-- 表单内容 -->
                        <div id="edit_box"></div>

                        <!-- 表单提交框 -->
                        <div style="text-align: -webkit-center;padding: 1em;background-color: #F2F3F5;">
                            <div class="form_button" @click="activeCollapse = ['2']">
                                {{ submit_button_name }}
                            </div>
                        </div>
                    </el-card>
                </div>
            </el-col>
            <!-- 右侧编辑属性区域 -->
            <el-col :span="6">
                <el-collapse v-model="activeCollapse">
                    <el-collapse-item title="组件属性" name="1" v-if="active_column">
                        <el-form @submit.prevent>
                            <!-- 组件名称 -->
                            <el-form-item
                                v-if="attributes.hasOwnProperty('title') && inArray(options[active_column].sourceType, ['hr', 'description', 'image']) == -1"
                                label="名称">
                                <el-input v-model="attributes.title"></el-input>
                            </el-form-item>

                            <el-form-item label="线型" v-if="options[active_column].sourceType == 'hr'">
                                <el-radio-group v-model="widget_borderstyle">
                                    <div><el-radio label="solid">实线</el-radio></div>
                                    <div><el-radio label="dashed">虚线</el-radio></div>
                                    <div><el-radio label="dotted">点线</el-radio></div>
                                    <div><el-radio label="double">双实线</el-radio></div>
                                </el-radio-group>
                            </el-form-item>

                            <el-form-item label="组件类型" v-if="options[active_column].sourceType == 'tabular'">
                                <el-radio-group v-model="options[active_column].tabular_type"
                                    @change="tabular_type_change" style="display: block;">
                                    <div style="clear: both"><el-radio label="radio">单选</el-radio></div>
                                    <div><el-radio label="checkbox">多选</el-radio></div>
                                    <div><el-radio label="text">文本</el-radio></div>
                                    <div><el-radio label="rank">评分</el-radio></div>
                                </el-radio-group>
                            </el-form-item>

                            <el-form-item label="题目" v-if="options[active_column].sourceType == 'tabular'">
                                <el-input
                                        v-for="(item, index) in options[active_column].tabular_question"
                                        :value="item.question"
                                        @change="$set(options[active_column].tabular_question, index, {question: $event, name: item.name})">
                                    <el-button
                                        slot="append"
                                        icon="delete"
                                        @click="options[active_column].tabular_question.splice(index, 1)"></el-button>
                                </el-input>
                                <el-button
                                    type="text"
                                    @click="options[active_column].tabular_question.push({question: '题目', name: `tabular_${++increments}`})">增加</el-button>
                            </el-form-item>

                            <el-form-item label="选项" v-if="options[active_column].sourceType == 'tabular' && inArray(options[active_column].tabular_type, ['radio', 'checkbox']) > -1">
                                <el-input
                                        v-for="(item, index) in options[active_column].tabular_option"
                                        :value="item"
                                        @change="$set(options[active_column].tabular_option, index, $event)">
                                    <el-button
                                        slot="append"
                                        icon="delete"
                                        @click="options[active_column].tabular_option.splice(index, 1)"></el-button>
                                </el-input>
                                <el-button
                                    type="text"
                                    @click="options[active_column].tabular_option.push('选项')">增加</el-button>
                            </el-form-item>

                            <el-form-item label="列标题文字" v-if="options[active_column].sourceType == 'tabular' && inArray(options[active_column].tabular_type, ['radio', 'checkbox']) == -1">
                                <el-input v-model="options[active_column].tabular_text"></el-input>
                            </el-form-item>

                            <!-- 必选框 -->
                            <el-form-item
                                v-if="inArray(options[active_column].sourceType, ['hr', 'description', 'image', 'tabular']) == -1"
                                label="必选">
                                <el-switch
                                    v-model="options[active_column].widget_require"
                                    on-text="是"
                                    off-text="否"
                                    @change="render">
                                </el-switch>
                            </el-form-item>

                            <!-- 必选框 -->
                            <el-form-item
                                v-if="options[active_column].sourceType == 'image'"
                                label="图片两侧无边距">
                                <el-switch
                                    v-model="widget_fullwidth"
                                    on-text="是"
                                    off-text="否">
                                </el-switch>
                            </el-form-item>

                            <!-- 多行文本行数 -->
                            <el-form-item
                                v-if="attributes.type == 'textarea'"
                                label="行数">
                                <el-input v-model="textarea_rows" placeholder="最大 20 行"></el-input>
                            </el-form-item>

                            <!-- 评分最大分值 -->
                            <el-form-item
                                v-if="options[active_column].sourceType == 'rank' || options[active_column].tabular_type == 'rank'"
                                label="最大分值">
                                <el-input v-model="options[active_column].max_number" placeholder="最大分值"></el-input>
                            </el-form-item>

                            <!-- 评分默认分值 -->
                            <el-form-item
                                v-if="options[active_column].sourceType == 'rank' || options[active_column].tabular_type == 'rank'"
                                label="默认分值">
                                <el-input v-model="options[active_column].default_number" placeholder="默认分值"></el-input>
                            </el-form-item>

                            <!-- 备注 -->
                            <el-form-item
                                v-if="inArray(options[active_column].sourceType, ['hr', 'description', 'image', 'optiontree']) == -1"
                                label="备注">
                                <el-input
                                    v-model="widget_description"
                                    placeholder="备注"></el-input>
                            </el-form-item>

                            <!-- 文本描述内容 -->
                            <el-form-item
                                v-if="schema[active_column].sourceType == 'description'"
                                label="内容">
                                <el-input
                                    autosize
                                    type="textarea"
                                    v-model="widget_content"
                                    placeholder="内容"></el-input>
                            </el-form-item>
                            <el-form-item label="货币符号" v-if="options[active_column].sourceType == 'currency'">
                                <el-select v-model="widget_currency" allow-create filterable placeholder="货币符号">
                                    <el-option label="￥ 人民币" value="￥"></el-option>
                                    <el-option label="$ 美元" value="$"></el-option>
                                    <el-option label="€ 欧元" value="€"></el-option>
                                    <el-option label="£ 英镑" value="£"></el-option>
                                    <el-option label="JP¥ 日元" value="JP¥"></el-option>
                                    <el-option label="A$ 澳元" value="A$"></el-option>
                                    <el-option label="C$ 加拿大元" value="C$"></el-option>
                                    <el-option label="Fr. 瑞士法郎" value="Fr."></el-option>
                                    <el-option label="HK$ 港币" value="HK$"></el-option>
                                    <el-option label="฿ 泰铢" value="฿"></el-option>
                                    <el-option label="S$ 新加坡元" value="S$"></el-option>
                                    <el-option label="kr 瑞典克朗" value="kr"></el-option>
                                    <el-option label="kr 挪威克朗" value="kr"></el-option>
                                    <el-option label="zł 波兰兹罗提" value="zł"></el-option>
                                    <el-option label="kr. 丹麦克朗" value="kr."></el-option>
                                    <el-option label="₽ 俄罗斯卢布" value="₽"></el-option>
                                    <el-option label="Mex$ 墨西哥比索" value="Mex$"></el-option>
                                    <el-option label="₩ 韩元" value="₩"></el-option>
                                    <el-option label="R 南非兰特" value="R"></el-option>
                                    <el-option label="R$ 巴西雷亚尔" value="R$"></el-option>
                                    <el-option label="₹ 印度卢比" value="₹"></el-option>
                                    <el-option label="RM 马来西亚林吉特" value="RM"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item
                                v-if="attributes.enum instanceof Array"
                                label="选项">
                                <el-input
                                        v-for="(item, index) in attributes.enum"
                                        :value="item"
                                        @change="attribute_change($event, 'enum', index)">
                                    <el-button
                                        slot="append"
                                        icon="delete"
                                        @click="attribute_delete('enum', index)"></el-button>
                                </el-input>
                                <el-button
                                    type="text"
                                    @click="attributes_add('enum')">增加</el-button>
                            </el-form-item>
                            <!-- 图片下方描述文本 -->
                            <el-form-item
                                v-if="schema[active_column].sourceType == 'image'"
                                label="图片下方描述">
                                <el-input
                                    v-model="bottom_description"
                                    placeholder="内容"></el-input>
                            </el-form-item>
                            <!-- 图片组件上传图片 -->
                            <el-form-item
                                v-if="options[active_column].type == 'image'"
                                label="图片">
                                <el-upload
                                    class="avatar-uploader"
                                    action="/form/upload"
                                    :data="{
                                        _token: csrf_token
                                    }"
                                    :show-file-list="false"
                                    :on-success="uploadSuccess"
                                    :before-upload="beforeUpload">
                                    <el-button size="small" type="primary">点击上传</el-button>
                                </el-upload>
                                <el-button
                                    size="small" type="primary" @click="input_modal = true">输入地址</el-button>
                                <el-dialog title="输入图片 URL" v-model="input_modal" size="tiny">
                                    <el-form>
                                        <el-form-item>
                                            <el-input v-model="input_url"></el-input>
                                        </el-form-item>
                                    </el-form>
                                    <span slot="footer" class="dialog-footer">
                                        <el-button @click="input_modal = false; input_url = ''">取消</el-button>
                                        <el-button type="primary" @click="set_user_url">确定</el-button>
                                    </span>
                                </el-dialog>
                            </el-form-item>
                            <el-form-item label="编辑" v-if="options[active_column].sourceType == 'multi_select'">
                                <el-button @click="show_multi_select_edit">批量编辑选项</el-button>
                            </el-form-item>
                            <el-form-item label="自定义字段" v-if="developer_mode">
                                <el-input type="textarea" v-model.lazy="developer_options" placeholder="key:value" autosize></el-input>
                            </el-form-item>
                            <el-form-item label="" v-if="options[active_column].sourceType == 'multi_select'">
                                <el-tabs type="border-card" @tab-click="set_tab_index">
                                    <el-tab-pane label="第 1 级">
                                        <el-form-item label="选项">
                                            <el-input
                                                    @change="change_tree_item(item.id, $event)"
                                                    v-for="(item, index) in find_items(options[active_column].tree, {}, 1)"
                                                    :value="item.value">
                                                <el-button
                                                    @click="delete_tree_item(item.id)"
                                                    slot="append"
                                                    icon="delete"></el-button>
                                            </el-input>
                                            <el-button
                                                @click="add_tree_item(activeTreeWhere)"
                                                type="text">增加</el-button>
                                        </el-form-item>
                                    </el-tab-pane>
                                    <el-tab-pane label="第 2 级">
                                        <el-form-item label="当 1 级选择">
                                            <el-select v-model="activeTreeWhere.line_1" placeholder="请选择">
                                                <el-option
                                                v-for="item in find_items(options[active_column].tree, {}, 1)"
                                                :label="item.value"
                                                :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="选项" v-if="activeTreeWhere.line_1">
                                            <el-input
                                                    @change="change_tree_item(item.id, $event)"
                                                    v-for="(item, index) in find_items(options[active_column].tree, activeTreeWhere, 2)"
                                                    :value="item.value">
                                                <el-button
                                                    @click="delete_tree_item(item.id)"
                                                    slot="append"
                                                    icon="delete"></el-button>
                                            </el-input>
                                            <el-button
                                                @click="add_tree_item(activeTreeWhere)"
                                                type="text">增加</el-button>
                                        </el-form-item>
                                    </el-tab-pane>
                                    <el-tab-pane label="第 3 级">
                                        <el-form-item label="当 1 级选择">
                                            <el-select v-model="activeTreeWhere.line_1" placeholder="请选择">
                                                <el-option
                                                v-for="item in find_items(options[active_column].tree, {}, 1)"
                                                :label="item.value"
                                                :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="当 2 级选择">
                                            <el-select v-model="activeTreeWhere.line_2" placeholder="请选择">
                                                <el-option
                                                v-if="activeTreeWhere.line_1"
                                                v-for="item in find_items(options[active_column].tree, {line_1: activeTreeWhere.line_1}, 2)"
                                                :label="item.value"
                                                :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="选项" v-if="activeTreeWhere.line_1 && activeTreeWhere.line_2">
                                            <el-input
                                                    @change="change_tree_item(item.id, $event)"
                                                    v-for="(item, index) in find_items(options[active_column].tree, activeTreeWhere, 3)"
                                                    :value="item.value">
                                                <el-button
                                                    @click="delete_tree_item(item.id)"
                                                    slot="append"
                                                    icon="delete"></el-button>
                                            </el-input>
                                            <el-button
                                                @click="add_tree_item(activeTreeWhere)"
                                                type="text">增加</el-button>
                                        </el-form-item>
                                    </el-tab-pane>
                                    <el-tab-pane label="第 4 级">
                                        <el-form-item label="当 1 级选择">
                                            <el-select v-model="activeTreeWhere.line_1" placeholder="请选择">
                                                <el-option
                                                v-for="item in find_items(options[active_column].tree, {}, 1)"
                                                :label="item.value"
                                                :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="当 2 级选择">
                                            <el-select v-model="activeTreeWhere.line_2" placeholder="请选择">
                                                <el-option
                                                v-if="activeTreeWhere.line_1"
                                                v-for="item in find_items(options[active_column].tree, {line_1: activeTreeWhere.line_1}, 2)"
                                                :label="item.value"
                                                :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="当 3 级选择">
                                            <el-select v-model="activeTreeWhere.line_3" placeholder="请选择">
                                                <el-option
                                                v-if="activeTreeWhere.line_2"
                                                v-for="item in find_items(options[active_column].tree, {line_1: activeTreeWhere.line_1, line_2: activeTreeWhere.line_2}, 3)"
                                                :label="item.value"
                                                :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                        <el-form-item label="选项" v-if="activeTreeWhere.line_1 && activeTreeWhere.line_2 && activeTreeWhere.line_3">
                                            <el-input
                                                    @change="change_tree_item(item.id, $event)"
                                                    v-for="(item, index) in find_items(options[active_column].tree, activeTreeWhere, 4)"
                                                    :value="item.value">
                                                <el-button
                                                    @click="delete_tree_item(item.id)"
                                                    slot="append"
                                                    icon="delete"></el-button>
                                            </el-input>
                                            <el-button
                                                @click="add_tree_item(activeTreeWhere)"
                                                type="text">增加</el-button>
                                        </el-form-item>
                                    </el-tab-pane>
                                </el-tabs>
                            </el-form-item>
                        </el-form>
                        <el-button @click="save_attributes" v-loading="save_loading">保存</el-button>
                        <el-button type="text" @click="delete_column">删除</el-button>
                    </el-collapse-item>
                    <el-collapse-item title="表单属性" name="2">
                        <el-form>
                            <el-form-item label="表单名称">
                                <el-input v-model="form_edit.form_name" @change="save_form"></el-input>
                            </el-form-item>
                            <el-form-item label="提交按钮">
                                <el-input v-model="submit_button_name" @change="save_form"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-checkbox v-model="form_edit.is_top" @change="save_form">置顶</el-checkbox>
                                <el-checkbox v-model="form_edit.is_follow" @change="save_form">关注</el-checkbox>
                            </el-form-item>
                        </el-form>
                    </el-collapse-item>
                </el-collapse>
            </el-col>
        </el-row>
        <el-dialog title="选项" v-if="developer_modal" v-model="developer_modal" size="small">
            <el-form label-width="6em">
                <el-form-item label="开发者模式">
                    <el-switch v-model="developer_mode" on-text="打开" off-text="关闭"></el-switch>
                </el-form-item>
            </el-form>
        </el-dialog>
        <el-dialog title="批量编辑选项" v-if="multi_select_edit" v-model="multi_select_edit" size="small">
            <el-form>
                <el-form-item label="批量编辑选项">
                    <el-input type="textarea" autosize v-model="multi_select_edit_content"></el-input>
                </el-form-item>
            </el-form>
            <el-button @click="render_tree">保存</el-button>
        </el-dialog>
    </div>
</template>
<style>
    .widget-item {
        cursor: pointer;
        text-align: center;
        padding: 0.2em;
        border-right: 1px solid #D9DFE2;
        border-bottom: 1px solid #D9DFE2;
        background-color: white;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0) inset;
    }
    .widget-item i {
        color: #2D8FD9;
    }
    .widget-item:hover {
        box-shadow: 0 0 0 1px #62ABE8 inset;
    }
    #widgets-box {
        border-top: 1px solid #D9DFE2;
        border-left: 1px solid #D9DFE2;
    }

    .widget-item-drop {
        float: left;
        position: relative;
        width: 100%;
        height: 40px;
        color: #AAA;
        background-color: #F6F9FB;
        border: none;
    }

    .widget-item-line {
        position: absolute;
        top: 50%;
        left: 8px;
        right: 8px;
        margin-top: -1px;
        border-top: 2px dashed #ccc;
    }

    .widget-item-text {
        position: relative;
        line-height: 20px;
        padding: 10px;
        background-color: #F6F9FB;
        display: inline-block;
    }

    #widgets-box .widget-item .widget-item-drop {
        display: none;
    }
    .drop_box {
        background-color: #E7E7EB;
    }
    #edit_box {
        height: 24em;
        overflow-y: scroll;
    }
    #edit_box .widget-item {
        background-color: rgb(238, 245, 246)!important;
        width: 100%;
    }
    #edit_box .widget-item .widget-item-description {
        display: none;
    }
    .alpaca-field-object {
        border: none!important;
    }
    .alpaca-container-item {
        margin-top: 0!important;
        border: 1px dashed rgba(0, 0, 0, 0);
    }
    .alpaca-image-display img {
        max-width: 100%;
    }
    #edit_box .sortable-chosen, .active-column {
        background-color: #FFF8DC;
        border: 1px dashed #CCC;
    }
    .help-block {
        display: none!important;
    }
    pre {
        white-space: pre-wrap;
    }
    .form_button {
        display: inline-block;
        white-space: nowrap;
        cursor: pointer;
        background: #3b67a0;
        color: #fff;
        padding: 10px 0;
        font-size: 18px;
        border-radius: 4px;
        background: rgb(59, 103, 160);
        padding: 9px 32px;
    }
    .required:after{
        content: " *";
        color: #ff4949;
        margin-right: 4px;
    }

    #edit_box table {
        border-collapse: collapse;
        border-spacing: 0;
        vertical-align: middle;
        width: 100%;
        background-color: #fff;
        border: 1px solid #D3D3D3;
    }

    #edit_box table td{
        border: 1px solid #D3D3D3;
        padding: 5px;
        display: table-cell;
        vertical-align: inherit;
    }

    #edit_box table td .radio_div,.checkbox_div,p{
        height: 29px;
        min-height: 29px;
        line-height: 29px;
        font-size: 15px;
        font-weight: normal;
        color: #000000;
        text-align: center;
        margin:0px;
    }

    #edit_box table td .text_div input{
        height: 25px;
        width:98%;
    }

    #edit_box table td .rank_div{
        text-align: center;
        font-size: 25px;
        cursor: pointer;
        -webkit-tap-highlight-color: rgba(255,255,255,0);
    }
</style>
<script>
    import Sortable from 'sortablejs'
    export default {
        data: function () {
            return {
                loading: true,
                // 表单属性
                form: {},
                form_edit: {},
                // 预定义组件
                widgets: [],
                schema: {},
                options: {},
                active_column: "",
                increments: 0,
                save_loading: false,
                template_save_loading: false,
                attributes: {},
                form_data: {},
                input_modal: false,
                input_url: "",
                activeCollapse: ["1", "2"],
                submit_button_name: "提交",
                developer_modal: false,
                developer_mode: false,
                developer_options: "",
                multi_select_edit: false,
                activeTreeIndex: 1, // 最小为 1
                activeTreeWhere: {
                    line_1: "",
                    line_2: "",
                    line_3: "",
                    line_4: ""
                },

                // 用户输入的数据
                textarea_rows: 6,
                widget_description: "",
                widget_content: "",
                bottom_description: "",
                widget_fullwidth: false,
                widget_borderstyle: "",
                widget_currency: "￥", // 货币符号
                multi_select_edit_content: ""
            }
        },
        computed: {
            id: function () {
                return this.$route.query.id
            },
            // 完整的 Schema，用于 Render 和保存
            full_schema: function () {
                return {
                    increments: this.increments,
                    submit_button_name: this.submit_button_name,
                    data: this.form_data,
                    schema: {
                        type: "object",
                        properties: this.schema
                    },
                    options: {
                        form: {},
                        fields: this.options
                    },
                    view: {
                        locale: "zh_CN"
                    },
                    postRender: function(control) {
                        window.alpaca = control
                        $vm.render()
                    },
                    developer_mode: this.developer_mode
                }
            },
            csrf_token: function () {
                return $("[name=csrf]").attr("content")
            }
        },
        watch: {
            id: function () {
                this.get_form()
            },
            active_column: function () {
                let {active_column, schema, options} = this
                if (active_column && this.schema[active_column]) {
                    this.activeCollapse = ["1"]
                    this.attributes = JSON.parse(JSON.stringify(schema[active_column]))
                    this.textarea_rows = options[active_column].rows
                    this.widget_description = options[active_column].widget_description
                    this.widget_content = options[active_column].widget_content
                    this.bottom_description = options[active_column].bottom_description
                    this.widget_fullwidth = options[active_column].widget_fullwidth ? true : false
                    this.widget_borderstyle = options[active_column].widget_borderstyle ? options[active_column].widget_borderstyle : "solid"
                    this.widget_currency = options[active_column].prefix
                    let developer_options = options[active_column].developer_options
                    if (!developer_options || developer_options instanceof Array) {
                        this.developer_options = ""
                    } else {
                        // 对象转换为文本
                        let result = [],
                            key
                        for (key in developer_options) {
                            if (!developer_options[key]) {
                                result.push(key)
                            } else {
                                result.push(`${key}:${developer_options[key]}`)
                            }
                        }
                        this.developer_options = result.join("\n")
                    }
                } else {
                    this.attributes = false
                }
            },
            textarea_rows: function (now) {
                if (now > 20) {
                    now = 20
                }
                if (this.schema[this.active_column].type == 'textarea') {
                    this.$set(this.options[this.active_column], 'rows', now)
                }
            },
            widget_description: function (now) {
                if (this.schema[this.active_column]) {
                    this.$set(this.options[this.active_column], 'widget_description', now)
                }
            },
            widget_content: function (now) {
                if (this.schema[this.active_column]) {
                    this.$set(this.options[this.active_column], 'widget_content', now)
                }
            },
            widget_borderstyle: function (now) {
                if (this.schema[this.active_column]) {
                    this.$set(this.options[this.active_column], 'widget_borderstyle', now)
                    if (this.schema[this.active_column].sourceType == 'hr') {
                        this.render_hr(this.active_column)
                    }
                }
            },
            widget_currency: function (now) {
                if (this.schema[this.active_column]) {
                    this.$set(this.options[this.active_column], 'prefix', now)
                }
            },
            developer_options: function () {
                let now = this.developer_options
                if (now && this.schema[this.active_column]) {
                    // 文本转换为对象
                    let rows = now.split("\n"),
                        result = {}
                    for (let i = 0; i < rows.length; i++) {
                        if (rows[i]) {
                            let line = rows[i].split(":")
                            result[line[0]] = line[1]
                        }
                    }
                    this.$set(this.options[this.active_column], 'developer_options', result)
                }
            },
            bottom_description: function (now) {
                if (this.schema[this.active_column]) {
                    let name = this.active_column,
                        path = this.form_data[name],
                        description = now,
                        fullwidth = this.widget_fullwidth

                    this.set_image_url(name, path, description, fullwidth)

                    this.$set(this.options[this.active_column], 'bottom_description', now)
                }
            },
            widget_fullwidth: function (now) {
                if (this.schema[this.active_column]) {
                    let name = this.active_column,
                        path = this.form_data[name],
                        description = this.bottom_description,
                        fullwidth = now

                    this.set_image_url(name, path, description, fullwidth)

                    this.$set(this.options[this.active_column], 'widget_fullwidth', now)
                }
            }
        },
        methods: {
            get_components: function () {
                let self = this

                this.$http.get("/form/components").then(function (response) {
                    let components = response.data
                    for (let i = 0; i < components.length; i++) {
                        self.widgets.push({
                            schema: components[i].schema ? JSON.parse(components[i].schema) : components[i].schema,
                            option: components[i].option ? JSON.parse(components[i].option) : components[i].option,
                            icon: components[i].icon_name
                        })
                    }
                    self.$nextTick(function () {
                        let items = $("#widgets-box")[0]
                        const sortable = Sortable.create(items, {
                            sort: false,
                            group: {
                                name: "widget",
                                pull: "clone",
                                put: false
                            },
                            setData: function (dataTransfer, dragEl) {
                                // $(dragEl).toggleClass("col")
                                if (dragEl.getAttribute) {
                                    dataTransfer.setData("type", dragEl.getAttribute("data-type"))
                                    dataTransfer.setData("schema", JSON.stringify(dragEl.getAttribute("data-schema")))
                                    dataTransfer.setData("option", JSON.stringify(dragEl.getAttribute("data-option")))
                                }
                            },
                            onEnd: function (evt) {
                                let type = evt.clone.dataset.type,
                                    schema = JSON.parse(evt.clone.dataset.schema),
                                    option = JSON.parse(evt.clone.dataset.option),
                                    index = evt.newIndex,
                                    options = self.options,
                                    name

                                for (name in options) {
                                    if (options[name].order >= index) {
                                        self.$set(options[name], 'order', options[name].order + 1)
                                    }
                                }

                                option.order = index

                                if (type == "null" || !type) {
                                    // this.$message.error("错误的组件，缺少组件类型属性")
                                } else {
                                    ++self.increments
                                    let name
                                    if (schema.sourceType) {
                                        if (schema.sourceType == 'image') {
                                            delete schema.title
                                        }
                                        name = `column-${schema.sourceType}-${self.increments}`
                                    } else {
                                        name = `column-${type}-${self.increments}`
                                    }
                                    if (option.sourceType == 'image') {
                                        self.$set(self.form_data, name, "/img/default.png")
                                    } else if (option.sourceType == 'multi_select') {
                                        option.tree = self.encode_tree("选项1\n选项2")
                                    }

                                    self.$set(self.schema, name, schema)
                                    self.$set(options, name, option)
                                    self.active_column = name

                                    self.render()
                                    self.save_form()
                                }
                            }
                        })
                    })
                    self.get_form()
                })
            },
            get_form: function () {
                this.loading = true
                this.$http.get("/form/detail", {params:{
                    id: this.id
                }}).then(function (response) {
                    $('html, body').animate({
                        scrollTop: $("#editable_box").offset().top
                    }, 1000)

                    this.loading = false
                    if (response.data.is_follow > 0) {
                        response.data.is_follow = true
                    }
                    if (response.data.is_top > 0) {
                        response.data.is_top = true
                    }

                    // 拆解 Schema
                    if (response.data.form_schema) {
                        let full_schema = JSON.parse(response.data.form_schema)
                        if (full_schema.schema.properties instanceof Array) {
                            this.schema = {}
                        } else {
                            this.schema = full_schema.schema.properties
                        }
                        if (full_schema.options.fields instanceof Array) {
                            this.options = {}
                        } else {
                            this.options = full_schema.options.fields
                        }
                        if (full_schema.data instanceof Array) {
                            this.form_data = {}
                        } else {
                            this.form_data = full_schema.data
                        }
                        if (full_schema.increments instanceof Array) {
                            this.increments = {}
                        } else {
                            this.increments = full_schema.increments
                        }
                        if (full_schema.hasOwnProperty('submit_button_name')) {
                            this.submit_button_name = full_schema.submit_button_name
                        } else {
                            this.submit_button_name = "提交"
                        }
                        this.developer_mode = full_schema.developer_mode ? true : false
                    }

                    this.active_column = ""

                    // 一份展示 一份编辑
                    this.form = response.data
                    this.form_edit = response.data

                    $("#edit_box").alpaca(this.full_schema)
                })
            },
            attribute_change: function (value, attribute, index) {
                if (this.active_column) {
                    this.$set(this.attributes[attribute], index, value)
                    this.$set(this.options[this.active_column], "optionLabels", this.attributes[attribute])
                }
            },
            attribute_delete: function (key, index) {
                if (this.active_column) {
                    this.attributes[key].splice(index, 1)
                    this.$set(this.options[this.active_column], "optionLabels", this.attributes.enum)
                }
            },
            attributes_add: function (key) {
                if (this.active_column) {
                    this.attributes[key].push("")
                    this.$set(this.options[this.active_column], "optionLabels", this.attributes.enum)
                }
            },
            inArray: function (value, array) {
                return $.inArray(value, array);
            },
            delete_column: function () {
                this.$alert(`是否确认删除字段？`, `删除字段`, {
                    confirmButtonText: '确定',
                    callback: (action) => {
                        if (action == "confirm") {
                            alpaca.removeItem(this.active_column, (item) => {})
                            delete this.schema[this.active_column]
                            delete this.form_data[this.active_column]
                            if (this.options && this.options[this.active_column]) {
                                delete this.options[this.active_column]
                            }
                            this.active_column = ""
                            this.$message({
                                type: 'info',
                                message: "删除成功"
                            })
                            this.save_form()
                        }
                    }
                })
            },
            save_form: function () {
                let form = this.form_edit,
                    self = this
                this.save_loading = true
                this.set_sort()
                let full_schema = JSON.parse(JSON.stringify(this.full_schema))
                this.$http.post("/form/save", {
                    id: this.form.id,
                    schema: full_schema,
                    form: {
                        // 仅传递需要更新的信息
                        form_name: form.form_name,
                        is_top: form.is_top,
                        is_follow: form.is_follow
                    }
                }).then(function (response) {
                    self.save_loading = false
                    if (!response.data.success) {
                        self.$message(response.data.message)
                    }
                }).catch(function (error) {
                    self.save_loading = false
                    self.$message.error("网络错误")
                })
            },
            save_template: function () {
                let form = this.form_edit,
                    self = this
                this.template_save_loading = true
                this.set_sort()
                this.$http.post("/form/template/create", {
                    schema: this.full_schema,
                    name: form.form_name
                }).then(function (response) {
                    self.template_save_loading = false
                    self.$message(response.data.message)
                }).catch(function (error) {
                    self.template_save_loading = false
                    self.$message.error("网络错误")
                })
            },
            render: function () {
                let self = this
                alpaca.refresh(function () {
                    // padding 样式
                    $(".alpaca-container-item")
                        .css("padding", "4px 20px")
                    $(".drop_box .box-card .el-card__body")
                        .css("padding", 0)

                    // 排序
                    const sortable = Sortable.create(document.getElementsByClassName("alpaca-container")[0], {
                        group: {
                            name: "widget",
                            pull: true
                        },
                        onEnd: function (evt, originalEvent) {
                            // 排序 保存表单
                            $vm.save_form()
                        }
                    })

                    let description = $("#edit_box [data-alpaca-container-item-name*=description]")
                    for (let i = 0; i < description.length; i++) {
                        let item = $(description[i]),
                            name = item.attr("data-alpaca-container-item-name")
                        
                        if ($vm.options[name] && $vm.options[name].widget_content) {
                            item.html("<pre>"+$vm.options[name].widget_content+"</pre>")
                        }
                    }

                    // 焦点事件
                    $("#edit_box .alpaca-container-item").unbind("click").click(function () {
                        let name = $(this).attr("data-alpaca-container-item-name")
                        if (name) {
                            $vm.active_column = name
                        }

                        $(".active-column").toggleClass("active-column")
                        $(this).addClass("active-column")
                    })
                    $(".control-label[for]").attr("for", null)

                    // 设置 image 类型
                    let {options, active_column, attributes} = self,
                        name

                    for (name in options) {
                        let item = options[name],
                            sourceType = item.sourceType
                        // 给组件设置居右介绍
                        if (item.widget_description) {
                            $(`#edit_box [data-alpaca-field-name=${name}] .control-label`).css("display", "inline-block")
                            $(`#edit_box [data-alpaca-field-name=${name}] .control-label`).after('<span style="margin-left: 1em">' + item.widget_description + '</span>')
                        }
                        // 给组件加星
                        if (item.widget_require) {
                            $(`#edit_box [data-alpaca-field-name=${name}] .control-label`).addClass("required")
                        }
                        // 图片组件 设置 URL
                        if (sourceType == 'image') {
                            let path = self.form_data[name],
                                description = item.bottom_description,
                                fullwidth = item.widget_fullwidth

                            self.set_image_url(name, path, description, fullwidth)
                        } else if (sourceType == 'hr') {
                            self.render_hr(name)
                        } else if (sourceType == 'currency') {
                            $(`[name=${name}]`).val(item.prefix)
                        } else if (sourceType == 'rank') {
                            let content = '<div style="font-size: 24px;">'
                            for (let i = 0; i < item.default_number; i++) {
                                content += '<i style="color:#f7ba2a;" class="ion-android-star"></i>'
                            }
                            for (let i = 0; i < item.max_number - item.default_number; i++) {
                                content += '<i style="color:#c6d1de;" class="ion-android-star-outline"></i>'
                            }
                            $(`#edit_box [name=${name}]`).replaceWith(content + '</div>')
                        } else if (sourceType == 'multi_select' || sourceType == 'country') {
                            let max_deep = parseInt(item.tree.max_deep),
                                deep = max_deep < 2 ? 2 : (max_deep > 4 ? 4 : max_deep),
                                content = ""

                            for (let i = 0; i < deep; i++) {
                                content += `<div class="optiontree-selector"><div class="form-group alpaca-field alpaca-field-select alpaca-optional alpaca-create alpaca-top"><select class="alpaca-control form-control"><option>请选择</option></select></div></div>`
                            }
                            $(`div[name=${name}]`).css("display", "block")
                            $(`div[name=${name}]`).html(content)
                            $(`input[name=${name}]`).remove()
                        } else if (sourceType == 'tabular') {
                            let tabular_content = '<table><tr><td></td>',
                                tabular_option = item.tabular_option;

                            if (item.tabular_type == 'text' || item.tabular_type == 'rank') {
                                tabular_option = [item.tabular_text]
                            }

                            for (let i = 0; i < tabular_option.length; i++) {
                                tabular_content += '<td><p>'+tabular_option[i]+'</p></td>';
                            }
                            
                            tabular_content += '</tr>';
                            for (let i = 0; i < item.tabular_question.length; i++) {
                                tabular_content += '<tr><td style="width:130px;font-size:15px;"><p style="text-align:left;">'+item.tabular_question[i].question+'</p></td>';
                                for (var k = 0; k < tabular_option.length; k++) {
                                    if(options[name].tabular_type == "radio" || options[name].tabular_type == "checkbox"){
                                        tabular_content += '<td><div class="'+options[name].tabular_type+'_div"><input type="'+options[name].tabular_type+'" name="'+options[name].tabular_question[i].name+'" value="'+tabular_option[k]+'" data-id="'+name+'"></div></td>';
                                    }else if(options[name].tabular_type == "text"){
                                        tabular_content += '<td><div class="'+options[name].tabular_type+'_div"><input type="'+options[name].tabular_type+'" name="'+options[name].tabular_question[i].name+'" data-id="'+name+'"></div></td>';
                                    }else if(options[name].tabular_type == "rank"){
                                        tabular_option = ["请选择"]
                                        tabular_content += '<td><div class="rank_div">';
                                        for (var n = 0; n < options[name].default_number; n++) {
                                            tabular_content += '<i id="' + name + '-' + options[name].tabular_question[i].name + '-' + n + '" data-num="' + n + '" style="color:#f7ba2a;" class="ion-android-star" data="' + options[name].max_number + '" name="'+options[name].tabular_question[i].name+'" data-id="'+name+'"></i>';
                                        }
                                        for (var n = options[name].default_number; n < options[name].max_number; n++) {
                                            tabular_content += '<i id="' + name + '-' + options[name].tabular_question[i].name + '-' + n + '" data-num="' + n + '" style="color:#c6d1de;" class="ion-android-star-outline" data="' + options[name].max_number + '" name="'+options[name].tabular_question[i].name+'" data-id="'+name+'"></i>'
                                        }
                                        tabular_content += '</div></td>';
                                    }
                                }
                                tabular_content += '</tr>';
                            }
                            tabular_content += '</table>';
                            $("#edit_box [name=" + name + "]").replaceWith(tabular_content)
                        }
                    }

                    // 禁止点击
                    $("#edit_box [name], #edit_box input").css("pointer-events", "none")

                    $("#edit_box").height($(window).height() - 322)
                })
            },
            render_hr: function (name) {
                let style = "",
                    extera,
                    item = this.options[name]

                if (item.widget_borderstyle) {
                    extera = item.widget_borderstyle == 'double' ? "border-top-width: 4px;" : ""
                    style = `style="border-style: ${item.widget_borderstyle};${extera}"`
                }
                $(`#edit_box [data-alpaca-container-item-name=${name}]`).html(`<hr ${style}/>`)
            },
            edit: function () {
                this.$router.push('/form/publish?id=' + this.id)
            },
            set_sort: function () {
                // 获得 sort 的顺序
                let element = $(".alpaca-container-item"),
                    options = this.options,
                    count = 0

                if (!options) {
                    options = {}
                }

                for (let i = 0; i < element.length; i++) {
                    let name = $(element[i]).attr("data-alpaca-container-item-name")
                    if (name) {
                        if (options[name]) {
                            options[name].order = ++count
                        } else {
                            options[name] = {
                                order: ++count
                            }
                        }
                    }
                }

                this.options = options

                return options
            },
            save_attributes: function () {
                let {options, active_column, attributes} = this,
                    name
                for (name in options) {
                    if (options[name].sourceType == 'multi_select') {
                        options[name].tree = this.encode_tree(this.decode_tree(options[name].tree))
                    }
                }
                if (active_column) {
                    if (parseInt(options[active_column].max_number) > 20) {
                        this.$message("最大分值不能超出 20")
                        return
                    } else if (parseInt(options[active_column].max_number) < 2) {
                        this.$message("最大分值必须大于 2")
                        return
                    } else if (parseInt(options[active_column].default_number) > parseInt(options[active_column].max_number) && parseInt(options[active_column].max_number) > 0) {
                        this.$message("默认分值不能大于最大分值且大于 0")
                        return
                    }

                    if (options[active_column].max_number > 0) {
                        options[active_column].max_number = parseInt(options[active_column].max_number)
                        options[active_column].default = parseInt(options[active_column].default_number)
                    }


                    if (options[active_column] && options[active_column].label) {
                        // 判断有 label 属性，把 title 写过去
                        this.$set(this.options[active_column], "label", attributes.title)
                    }
                    this.$set(this.schema, active_column, attributes)
                    this.render()
                    this.save_form()
                }
            },
            set_user_url: function () {
                this.$set(this.form_data, this.active_column, this.input_url)
                this.set_image_url(this.active_column,
                    this.input_url,
                    this.bottom_description,
                    this.widget_fullwidth)
                this.save_form()
            },
            uploadSuccess: function (res, file) {
                if (!res.success) {
                    this.$message.error(res.message)
                } else {
                    let name = this.active_column,
                        path = res.path,
                        description = this.bottom_description,
                        fullwidth = this.widget_fullwidth

                    this.$set(this.form_data, name, path)
                    this.set_image_url(name, path, description, fullwidth)
                    this.save_form()
                }
            },
            beforeUpload: function (file) {
                const isLt2M = file.size / 1024 / 1024 < 2
                if (!isLt2M) {
                    this.$message.error('上传图片大小不能超过 2MB!');
                }
                return isLt2M
            },
            set_image_url: function (name, path, description, fullwidth) {
                // 仅把数据同步到 DOM，不再做保存到 Schema / Options 的操作
                // name 和 path 必填
                if (!name || !path) {
                    return false
                }

                // 关闭输入 URL 的框
                this.input_modal = false
                this.input_url = ""

                let content = `<img src="${path}">`

                if (description) {
                    content = content + `<p style="text-align: center;">${description}</p>`
                }

                if (fullwidth) {
                    $(`[data-alpaca-container-item-name=${name}]`).css("padding", "4px 0")
                } else {
                    $(`[data-alpaca-container-item-name=${name}]`).css("padding", "4px 20px")
                }

                $(`[name=${name}]`).html(content)
            },
            decode_tree: function (tree) {
                // 将 multi select 的 Object 转换成 Tab 换行的文本
                let string = "",
                    line_1 = this.find_items(tree, {}, 1),
                    result = []

                for (let i = 0; i < line_1.length; i++) {
                    result.push(line_1[i].value)

                    let line_2 = this.find_items(tree, {
                        line_1: line_1[i].attributes.line_1
                    }, 2)
                    for (let i2 = 0; i2 < line_2.length; i2++) {
                        result.push("-".repeat(1) + line_2[i2].value)

                        let line_3 = this.find_items(tree, {
                            line_1: line_2[i2].attributes.line_1,
                            line_2: line_2[i2].attributes.line_2
                        }, 3)

                        for (let i3 = 0; i3 < line_3.length; i3++) {
                            result.push("-".repeat(2) + line_3[i3].value)

                            let line_4 = this.find_items(tree, {
                                line_1: line_3[i3].attributes.line_1,
                                line_2: line_3[i3].attributes.line_2,
                                line_3: line_3[i3].attributes.line_3,
                            }, 4)
                            
                            for (let i4 = 0; i4 < line_4.length; i4++) {
                                result.push("-".repeat(3) + line_4[i4].value)
                            }
                        }
                    }
                }

                return result.join("\n")
            },
            encode_tree: function (tree) {
                // 将多级下拉的换行文本转换成 Object 格式
                let content = {
                    "selectors": {},
                    "data": [],
                    "horizontal": true
                },
                // 分割每一行
                lines = tree.split("\n"),
                // 查看上一行的值
                lines_array = {},
                // 曾经出现的最大层级
                max_deep = 1,
                datas = [],
                selectors = {},
                order = [],
                increments = 0

                for (let i = 0; i < lines.length; i++) {
                    let deep = lines[i].split("-").length,
                        line = lines[i].replace(new RegExp(/(-)/g), '')
                    
                    if (!line) {
                        continue
                    }
                    if (deep == 1) {
                        // 第一级，清除
                        lines_array = {}
                    } else if (deep > 4) {
                        continue
                    }
                    
                    max_deep = Math.max(max_deep, deep)

                    lines_array[`line_${deep}`] = line

                    let lines_attributes = {}
                    if (lines_array['line_1'] && deep >= 1) {
                        lines_attributes['line_1'] = lines_array['line_1']
                    }
                    if (lines_array['line_2'] && deep >= 2) {
                        lines_attributes['line_2'] = lines_array['line_2']
                    }
                    if (lines_array['line_3'] && deep >= 3) {
                        lines_attributes['line_3'] = lines_array['line_3']
                    }
                    if (lines_array['line_4'] && deep >= 4) {
                        lines_attributes['line_4'] = lines_array['line_4']
                    }
                    
                    datas.push({
                        value: line,
                        attributes: lines_attributes,
                        id: ++increments
                    })
                }

                // 计算标题
                for (let i = 1; i <= 4; i++) {
                    selectors[`line_${i}`] = {
                        "schema": {
                            "type": "string"
                        },
                        "options": {
                            "type": "select",
                            "noneLabel": "请选择"
                        }
                    }
                    order.push(`line_${i}`)
                }

                content.data = datas
                content.selectors = selectors
                content.order = order
                content.max_deep = max_deep
                content.increments = increments

                return content
            },
            render_tree: function () {
                this.multi_select_edit = false
                let tree = this.encode_tree(this.multi_select_edit_content)
                this.$set(this.options[this.active_column], "tree", tree)
                this.render()
            },
            show_multi_select_edit: function () {
                this.multi_select_edit = true
                this.multi_select_edit_content = this.decode_tree(this.options[this.active_column].tree)
            },
            find_items: function ({data}, where, index) {
                let activeTreeIndex = this.activeTreeIndex,
                    items = []

                if (!data) {
                    return items
                }

                for (let i = 0; i < data.length; i++) {
                    let line = data[i],
                        find_accept = true,
                        name,
                        count = Object.getOwnPropertyNames(line.attributes).length - 1 // remove __ob__

                    if (count != index) {
                        continue
                    }

                    for (name in where) {
                        if (where[name] && where[name] != line.attributes[name]) {
                            find_accept = false
                            continue
                        }
                    }

                    if (find_accept) {
                        items.push(line)
                    }
                }

                return items
            },
            set_tab_index: function ({index}) {
                // 最小为 1
                this.activeTreeIndex = parseInt(index) + 1
                this.activeTreeWhere = {
                    line_1: "",
                    line_2: "",
                    line_3: "",
                    line_4: ""
                }
            },
            delete_tree_item: function (id) {
                let tree = this.options[this.active_column].tree,
                    data = tree.data
                for (let i = 0; i < data.length; i++) {
                    if (data[i].id == id) {
                        data.splice(i, 1)
                        break
                    }
                }
                // this.$set(this.options[this.active_column], "tree", this.encode_tree(tree))
            },
            add_tree_item: function (where) {
                let option = this.options[this.active_column],
                    tree = option.tree,
                    data = tree.data,
                    increments = ++tree.increments,
                    attributes = {},
                    value = "选项" + increments,
                    items = [],
                    order = [],
                    name

                // 计算条件总数
                for (name in where) {
                    if (where[name]) {
                        items.push(where[name])
                    }
                }

                // 计算 attributes
                for (let i = items.length; i >= 1; i--) {
                    attributes[`line_${i}`] = items[i - 1]
                }

                attributes[`line_${items.length + 1}`] = value

                data.push({
                    value: value,
                    attributes: attributes,
                    id: increments
                })

                this.$set(this.options[this.active_column], "tree", tree)
            },
            change_tree_item: function (id, event) {
                let tree = this.options[this.active_column].tree,
                    data = tree.data
                for (let i = 0; i < data.length; i++) {
                    if (data[i].id == id) {
                        let length = Object.getOwnPropertyNames(data[i].attributes).length - 1

                        this.$set(data[i], "value", event)
                        this.$set(data[i].attributes, `line_${length}`, event)

                        break
                    }
                }
            },
            tabular_type_change: function (now) {
                this.render()
            }
        },
        mounted: function () {
            // 由于 AlpacaJS 中有回调函数，无法常规传递对象，所以暴露到全局
            window.$vm = this
            this.get_components()

            $('.step_upload .el-step__head').on("click", function() {
                $vm.edit()
            })

            $('.step_upload .el-step__title').on("click", function() {
                $vm.edit()
            })
        },
        destroyed: function () {
            // 销毁对象
            delete window.$vm
        }
    }
</script>