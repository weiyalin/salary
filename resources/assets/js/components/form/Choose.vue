<template>
    <div>
        <el-row  v-loading.body='load'>
            <el-col :span="24" style="overflow:auto;">
                <div class="el-cascader-menus" style="position:relative;margin:0 0;min-width:242px;min-height:400px;">
                    <ul v-for="(item, deep) in menu" class="el-cascader-menu" style="width:240px;height:400px;">
                        <p style="text-align:center;" v-if="!checkChildren(item)">没有数据</p>

                        <div v-if="deep == 0 && checkChildren(item)" style="text-align:center;border-bottom: 1px solid #d1e3e5;padding-bottom: 6px;">
                            <el-autocomplete
                                    placeholder="姓名/学号/工号/手机号"
                                    v-model="state"
                                    popper-class="my-autocomplete"
                                    custom-item="my-item-zh"
                                    :fetch-suggestions="searchResource"
                                    @select="searchCallback"
                                    icon="search"
                            ></el-autocomplete>
                        </div>

                        <div v-if="item['group'] && item['group'].length > 0">
                            <li class="el-cascader-menu__item" style="border-bottom: 1px solid #e4eef1;">
                                <span>分组</span>
                                <el-checkbox style="float:right;"  v-if="checkDisplay('group')" :value="checkChecked(item['group'])" @change="setListWhere(item['group'], 'group', $event.target.checked, deep)">全选</el-checkbox>
                            </li>
                            <li v-for="(line, i) in item['group']" @click="on_clickLine(line, deep, i, 'group')" class="el-cascader-menu__item el-cascader-menu__item--extensible" :class="{'is-active': checkSelect(deepIndex[deep], line)}">
                                {{ line.name ? line.name : real_name }}
                                <el-checkbox style="float:right;" @change="setList(line)"  v-model="line.checked" v-if="checkDisplay('group')"></el-checkbox>
                            </li>
                        </div>

                        <div v-if="item['org'] && item['org'].length > 0">
                            <li class="el-cascader-menu__item" style="border-bottom: 1px solid #e4eef1;">
                                <span>组织机构</span>
                                <el-checkbox style="float:right;"  v-if="checkDisplay('org')" :value="checkChecked(item['org'])" @change="setListWhere(item['org'], 'org', $event.target.checked, deep)">全选</el-checkbox>
                            </li>
                            <li v-for="(line, i) in item['org']" @click="on_clickLine(line, deep, i, 'org')" class="el-cascader-menu__item el-cascader-menu__item--extensible" :class="{'is-active': checkSelect(deepIndex[deep], line)}">
                                {{ line.name }}
                                <el-checkbox style="float:right;" @change="setList(line)"  v-model="line.checked" v-if="checkDisplay('org')"></el-checkbox>
                            </li>
                        </div>

                        <div v-if="item['member'] && item['member'].length > 0">
                            <li class="el-cascader-menu__item" style="border-bottom: 1px solid #e4eef1;">
                                <span>成员  {{ item['member_data']['current_page_1'] }}/{{ item['member_data']['last_page'] }} 页，共 {{ item['member_data']['total'] }} 人</span>
                                <el-checkbox style="float:right;"  v-if="checkDisplay('member')" :value="checkChecked(item['member'])" @change="setListWhere(item['member'], 'member', $event.target.checked, deep)">全选</el-checkbox>
                            </li>
                            <li v-for="(line, i) in item['member']" class="el-cascader-menu__item">
                                {{ line.name }}
                                <el-checkbox style="float:right;" @change="setList(line)"  v-model="line.checked" v-if="checkDisplay('member')"></el-checkbox>
                            </li>
                            <div style="text-align:center" v-if="item['member'] && item['member'].length > 0 && (!item['member_data'] || (item['member_data']['current_page_1'] < item['member_data']['last_page']))" class="load-more" @click="load_more(item, deep)">
                                <el-button loading v-if="loading[deep]">加载更多</el-button>
                                <el-button v-else>加载更多</el-button>
                            </div>
                        </div>
<!--                        <div  v-if="org && org.length > 0">
                            <li class="el-cascader-menu__item" style="border-bottom: 1px solid #e4eef1;">
                                <span>组织机构</span>
                                <el-checkbox style="float:right;" :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                            </li>
                            <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                                <li v-for="city in cities" class="el-cascader-menu__item el-cascader-menu__item&#45;&#45;extensible">
                                    <el-checkbox :label="city">{{city}}</el-checkbox>
                                </li>
                            </el-checkbox-group>
                        </div>-->
                    </ul>
                </div>
            </el-col>
        </el-row>
        <div class="list-tag">
            <span v-for="vo in list">
                <el-tag class="span-tag" v-if="vo.type=='org'" :closable="true" type="primary" @close="deleteTags(vo)">{{vo.name}}</el-tag>
                <el-tag class="span-tag" v-if="vo.type=='group'" :closable="true" type="success" @close="deleteTags(vo)">{{vo.name}}</el-tag>
                <el-tag class="span-tag" v-if="vo.type=='teacher'||vo.type=='student'" :closable="true" type="warning" @close="deleteTags(vo)">{{vo.name}}</el-tag>
            </span>
        </div>
        <div class="list-button">
            <el-button type="primary" @click="send">发送通知</el-button>
        </div>
    </div>
</template>
<style scoped>
    .list-tag{
        margin-top: 30px;
        padding: 5px;
        border: 1px solid #d1e3e5;
        background-color: #fff;
        width: 100%;
        min-height: 60px;
    }

    .list-button{
        width: 100%;
        text-align: center;
        margin-top: 20px;
    }

    .span-tag{
        margin: 5px;
    }
</style>
<script>
    import Vue from 'vue'
    Vue.component('my-item-zh', {
        functional: true,
        render: function (h, ctx) {
            var item = ctx.props.item;
            return h('li', ctx.data, [
                h('div', { attrs: { class: 'name' } }, [item.name]),
                h('span', { attrs: { class: 'addr' } }, [item.org_name])
            ]);
        },
        props: {
            item: { type: Object, required: true }
        }
    });
    export default {
        data() {
            return {
                options: [], // 数据源
                menu: [], // 列表，指向 options
                uniqueid: 0, // 前端用于判断这个对象的重复
                deepIndex: [], // 标识所在的 index，key 是 deep 层级
                loading: [], // 标识 deep 下面的 loading 是否显示

                searchItems: [],

                selectType: ['group', 'org', 'member'],
                list: [],

                load: false,
                state: "",
            }
        },
        props: {
            uri: {
                type: String,
                default: ""
            },
            show: {
                type: Boolean,
                default: false
            },
        },
        watch:{
            show(val){
                if(val){
                    this.menu = [];
                    this.getData({deep: 0, first: true})
                }
            }
        },
        methods: {
            // 设置列表对象
            setMenu(item, deep) {
                let children = item.children
                this.deleteDeep(this.menu, deep)
                if (children) {
                    // 有子节点，要展示新面板，并且删掉多余的面板
                    this.menu.push(children)
                }
            },
            // 给 item 下的所有 children 设置为指定 deep，type
            setDeep(item, deep) {
                if (item) {
                    for (let i = item.length - 1; i >= 0; i--) {
                        item[i]["deep"] = deep
                        item[i]["checked"] = false

                        let list = this.list
                        for (let n = list.length - 1; n >= 0; n--) {
                            if (list[n] && item[i]["type"] == list[n]["type"] && item[i]["id"] == list[n]["id"]) {
                                item[i]["checked"] = true
                            }
                        }
                    }
                }
            },
            // 从 item 中删掉索引大于 deep 的
            deleteDeep: function (item, deep) {
                for (let i = item.length - 1; i > deep; i--) {
                    item.splice(i, 1)
                }
            },
            setList(item) {
                if (item) {
                    let list = this.list
                    let deep = item.deep
                    let menu = this.menu
                    let deepIndex = this.deepIndex
                    // 设置其父级全部为不选中
                    if (!item.checked) {
                        // 不遍历最后一个
                        for (let i = deep; i >= 0; i--) {
                            if (deepIndex[i]) {
                                deepIndex[i].checked = item.checked
                            }
                        }
                    }

                    // 给所有子节点设置
                    if (item.children) {
                        this.setChecked(item.children['group'], item.checked)
                        this.setChecked(item.children['org'], item.checked)
                        this.setChecked(item.children['member'], item.checked)
                    }

                    // 如果有或没有做相应的处理
                    if (item.checked && deep > 0) {
                        let checkChecked = this.checkChecked
                        // 如果其同级节点全部被选中则删掉同级的全部 添加父级
                        let where = checkChecked(menu[deep]["group"]) &&
                                checkChecked(menu[deep]["org"]) &&
                                checkChecked(menu[deep]["member"])
                        if (where) {
                            // 删除全部同级
                            let parent = deepIndex[deep-1]
                            parent.checked = item.checked
                            this.$set(deepIndex, deep-1, parent)

                            if (this.checkDisplay(parent.type)) {
                                this.deleteSiblings(menu[deep]["group"])
                                this.deleteSiblings(menu[deep]["org"])
                                this.deleteSiblings(menu[deep]["member"])
                                this.deleteFromList(parent)
                                list.push(parent)
                            } else {
                                // 插入前删掉重复的
                                this.deleteFromList(item)
                                list.push(item)
                            }
                        } else {
                            // 插入前删掉重复的
                            if (menu[deep+1]) {
                                // 在有下一级的情况下清除掉下一级在 list 中
                                this.deleteSiblings(menu[deep+1]["group"])
                                this.deleteSiblings(menu[deep+1]["org"])
                                this.deleteSiblings(menu[deep+1]["member"])
                            }

                            this.deleteFromList(item)
                            list.push(item)
                        }
                    } else if (item.checked) {
                        // 插入前删掉重复的
                        for (let i = list.length - 1; i >= 0; i--) {
                            if (list[i]["type"] == item["type"] && list[i]["id"] == item["id"]) {
                                list.splice(i, 1)
                            }
                        }
                        list.push(item)
                    } else {
                        if (deep > 0) {
                            let parent = deepIndex[deep-1]
                            let find = this.deleteFromList(parent)

                            // 由于 parent 的 checked 此刻已经变成 false，只能从 list 里面判断了
                            if (find) {
                                this.addSiblings(menu[deep]["group"])
                                this.addSiblings(menu[deep]["org"])
                                this.addSiblings(menu[deep]["member"])
                            }
                        }

                        this.deleteFromList(item)
                    }
                }
            },
            deleteTags(obj) {
                obj.checked = false;
                this.list = this.list.filter(t => t.id != obj.id)
                //this.list.$remove(obj);
            },
            deleteSiblings(item) {
                // 从 list 删掉同级
                let list = this.list
                if (item) {
                    for (let i = item.length - 1; i >= 0; i--) {
                        this.deleteFromList(item[i])
                    }
                }
            },
            addSiblings(item) {
                // 从 list 删掉同级
                let list = this.list
                if (item) {
                    for (let i = item.length - 1; i >= 0; i--) {
                        list.push(item[i])
                    }
                }
            },
            deleteFromList(item) {
                let list = this.list
                let find = false
                if (item) {
                    for (let i = list.length - 1; i >= 0; i--) {
                        if (list[i]["type"] == item["type"] && list[i]["id"] == item["id"]) {
                            find = true
                            list.splice(i, 1)
                        }
                    }
                }
                return find
            },
            setListWhere(item, where, checked=false, deep=0) {
                this.deleteDeep(this.menu, deep)
                if (item) {
                    // 设置 item 中满足 type 是 where 的
                    for (let i = item.length - 1; i >= 0; i--) {
                        //if (item[i].type == where) {
                        let line = item[i]
                        line.checked = checked
                        this.$set(item, i, line)
                        //item.$set(i, line)

                        this.setList(item[i])
                    }
                }
            },
            setChecked(item, checked=false) {
                // 设置其子节点全部为 checked
                if (item) {
                    // 从顶级的 deepIndex 开始遍历
                    for (let i = item.length - 1; i >= 0; i--) {
                        // 构建一个深拷贝对象
                        item[i] = item[i]
                        item[i]["checked"] = checked
                        this.$set(item, i, item[i])
                        //item.$set(i, item[i])

                        if (item[i].children) {
                            this.setChecked(item[i].children['group'], checked)
                            this.setChecked(item[i].children['org'], checked)
                            this.setChecked(item[i].children['member'], checked)
                        }
                    }
                }
            },
            checkChecked(item) {
                // 检查 item 下面的 checked 是不是全是 true
                // 不检查 children
                if (item && item.length > 0) {
                    for (let i = item.length - 1; i >= 0; i--) {
                        if (!item[i].checked) {
                            return false
                        }
                    }
                    return true
                }
                // 由于要同时检查 group org member，对于空的直接就返回 true 也没有问题
                return true
            },
            getData(params, line = null) {
                // 从后端获取 Group 等数据
                if (line && line.children) {
                    this.setMenu(line, params.deep)
                    // 更新了数据后自动定位到最右边
                    this.$nextTick(function(){
                        let el = this.$el
                        window.el = el
                        if (el) {
                            el.scrollLeft = el.scrollWidth
                        }
                    })
                } else {
                    this.load = true;
                    this.$http.get('/form/selector', {params: params}).then(function (response)  {
                        let data = response.data;
                        this.load = false;
                        // 给首级的深度全部赋值为 0
                        if (this.menu.length == 0) {
                            let item = data.msg
                            item.deep = params.deep
                            this.setDeep(item['group'], params.deep)
                            this.setDeep(item['org'], params.deep)
                            this.setDeep(item['member'], params.deep)
                            this.options = item
                            this.menu.push(item)
                        } else {
                            let item = {}
                            let list = data.msg
                            if (line.type == 'group') {
                                item['member'] = list['member']
                                item['member_data'] = list['member_data']
                            } else if (line.type == 'org') {
                                item['org'] = list['org']
                                item['member'] = list['member']
                                item['member_data'] = list['member_data']
                            }
                            this.setDeep(item['group'], params.deep + 1)
                            this.setDeep(item['org'], params.deep + 1)
                            this.setDeep(item['member'], params.deep + 1)
                            line.children = item
                            if (this.checkSelect(this.deepIndex[params.deep], line)) {
                                this.setMenu(line, params.deep)
                            }
                        }
                        // 更新了数据后自动定位到最右边
                        this.$nextTick(function(){
                            let els = this.$el
                            let el = els.children[0].children
                            window.el = el
                            if (el) {
                                el.scrollLeft = el.scrollWidth
                            }

                            let width = (240 * this.menu.length) + 2 + "px";
                            $(".el-cascader-menus").css("width", width);
                        })
                    })
                }
            },
            checkSelect(deepSelect, line) {
                return deepSelect && deepSelect.id == line.id && deepSelect.type == line.type
            },
            checkChildren(item) {
                return (item['group'] && item['group'].length > 0) || (item['org'] && item['org'].length > 0) || (item['member'] && item['member'].length > 0)
            },
            on_clickLine(line, deep, i, type) {
                // 设置 loading 状态
                this.$set(this.loading, deep, false)
                // 设置背景颜色
                this.$set(this.deepIndex, deep, line)
                // 设置完成
                this.getData({deep: deep, type: type, id: line.id}, line)
            },
            load_more(item, deep) {
                // 加载更多用户
                this.$set(this.loading, deep, true)
                let page = item['member_data']['current_page'] + 1
                let params = {
                    deep: deep,
                    type: this.deepIndex[deep - 1]['type'],
                    page: page,
                    id: this.deepIndex[deep - 1]['id']
                }
                this.$http.get('/form/selector', {params: params}).then(function (response) {
                    let data = response.data;
                    let list = data.msg
                    let member = {}

                    member['member'] = list['member']
                    member['member_data'] = list['member_data']

                    let item = this.menu[params.deep]
                    if (item['member_data']['current_page'] + 1 == member['member_data']['current_page']) {
                    // 避免重复插入
                    item['member_data'] = member['member_data']
                    for (let i = member['member'].length - 1; i >= 0; i--) {
                        item['member'].push(member['member'][i])
                    }
                }
                this.$set(this.loading, deep, false)
            })
            },
            checkDisplay(type) {
                if (this.selectType.indexOf(type) != -1) {
                    return true
                }
            },
            send(){
                //过滤
                this.load = true;
                var acceptData = [];
                var tmp = null;
                var self = this;
                for (var i = 0; i < self.list.length; i++) {
                    tmp = self.list[i];
                    var item = {
                        type: tmp.type,
                        name: tmp.name
                    };
                    if (tmp.type == "teacher" || tmp.type == "student") {
                        item.id = tmp.relation_id;
                    } else {
                        item.id = tmp.id;
                    }
                    acceptData.push(item);
                }
                this.$http.post('/form/send', {list: acceptData, uri:this.uri}).then(function (response)  {
                    let data = response.data;
                    this.load = false;
                    if(data.status == 0){
                        this.$emit("on-show", 1);
                        this.$notify({
                            type: 'success',
                            message: '发送成功!',
                            duration: 2000,
                        });
                    }else{
                        this.$notify({
                            type: 'error',
                            message: '发送失败!',
                            duration: 2000,
                        });
                    }
                })
            },
            searchResource(keyword, cb) {
                let self = this
                if(self.state != ""){
                    self.$http.get("/form/selector/search", {params: {keyword: self.state}}).then(function (response) {
                        if(response.data.status == 0){
                            let data = response.data.msg.user;
                            for (let i = data.length - 1; i >= 0; i--) {
                                if (data[i] && data[i]["type"] == 1) {
                                    data[i]["type"] = "student"
                                } else if (data[i] && data[i]["type"] == 2) {
                                    data[i]["type"] = "teacher"
                                }
                            }
                            self.searchItems = data
                            cb(data)
                        }else{

                        }
                    })
                }else{
                    cb([])
                }
            },
            searchCallback(item) {
                if (item && item.type) {
                    if (item.type == 1) {
                        item.type = "student"
                    } else if (item.type == 2) {
                        item.type = "teacher"
                    }
                }
                let list = this.list
                // 删掉重复的
                for (let i = list.length - 1; i >= 0; i--) {
                    if (list[i]["type"] == item["type"] && list[i]["id"] == item["id"]) {
                        list.splice(i, 1)
                    }
                }
                // 插入新的
                list.push(item)
                this.state = item.name
                return item.name
            },
        },
        mounted(){
            this.getData({deep: 0, first: true})
        }
    }
</script>
