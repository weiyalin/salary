<template>
    <div style="font-size:14px;">
        <el-row>
            <el-col>
                <div ref="dt_handsontable"></div>
            </el-col>
        </el-row>
    </div>
</template>
<style scoped>
    @import "~handsontable/dist/handsontable.full.css";

</style>

<script>
    import Handsontable from 'handsontable/dist/handsontable.full.js'
    //import Form from './form'

    export default {
        props: {
            template: {
                type: Object,
                required: true,
                default: () => ({})
            },

        },
        data(){
//            let form = new Form();
//            let tpl = form.getInitTpl();
            return {
                //data:[],
                id:0,
                name:'',
                dt:{},
                settings :{
                    startRows: 1,
                    startCols: 50,
                    colHeaders: true,
                    rowHeaders: false,
                    maxRows:1,
                    minCols:50,
                    colWidths:100,
//                    manualColumnResize: true,
//                    manualRowResize: true,

//                    colWidths:[120,150,120,120,120,120,150],
//                    autoColumnSize: {syncLimit: '100%'},
//                    minSpareRows: 10,
                    height:100,

                },

            }
        },
        watch:{
            template(val){
                //console.log("tpl_id:"+val);
                this.settings.data = this.template.data;
                //this.data = this.template.data;
            },

        },
        mounted() {
            let self = this;
            self.settings.data = self.template.data;
            self.id = self.template.id;
            self.name = self.template.name;
            self.dt.table = new Handsontable(self.$refs.dt_handsontable, self.settings);
            self.dt.table.updateSettings({

                afterChange: function (changes, source) {
//                    console.log('change');
//                    console.log(changes);

                    self.$emit("on-data-change",{id:self.id,name:self.name,data:self.settings.data});
                }

            });
            console.log('Component mounted.')
        }
    }
</script>