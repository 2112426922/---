(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-0b10"],{"5YHe":function(e,t,i){"use strict";var a=i("Vuxe");i.n(a).a},Vuxe:function(e,t,i){},h4ox:function(e,t,i){"use strict";i.r(t);var a=i("t3Un");function r(e){return Object(a.a)({url:"accessRole/pageList",method:"GET",params:e})}function l(e){return Object(a.a)({url:"accessRole",method:"post",data:e})}function n(e){return Object(a.a)({url:"accessRole/delete/batch",method:"post",data:e})}function o(e){return Object(a.a)({url:"accessRole",method:"put",data:e})}function d(e){return Object(a.a)({url:"accessRole/"+e.id,method:"get",params:{accessKey:e.accessKey}})}function s(e){return Object(a.a)({url:"accessRole/authorityTree/"+e,method:"get"})}function c(e){return Object(a.a)({url:"accessRole/saveAuthorityTree",method:"post",data:e})}var u=i("cLjf"),p=i.n(u),h=i("hDQ3"),b=i.n(h),f={props:{visib:{required:!0,type:Boolean,default:!1},roleCode:{required:!0,type:String,default:function(){return""}}},data:function(){return{checkedKeys:[],treeData:[]}},watch:{visib:function(e){e&&(console.log(1),this.getTreeData())}},created:function(){},methods:{getTreeData:function(){var e=this;return b()(p.a.mark(function t(){var i,a,r;return p.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,s(e.roleCode);case 2:if(i=t.sent,a=i.code,r=i.data,"200"==a){t.next=7;break}return t.abrupt("return");case 7:e.treeData=r.treeData,e.checkedKeys=r.checkedKeys;case 9:case"end":return t.stop()}},t,e)}))()},saveTreeData:function(){var e=this;return b()(p.a.mark(function t(){var i,a;return p.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return i={roleCode:e.roleCode,authorityList:e.$refs.roleTree.getCheckedKeys(!0)},t.next=3,c(i);case 3:if(a=t.sent,"200"==a.code){t.next=7;break}return t.abrupt("return");case 7:e.closeDialog();case 8:case"end":return t.stop()}},t,e)}))()},closeDialog:function(){this.treeData=[],this.checkedKeys=[],this.$emit("handleClose")}}},g=(i("5YHe"),i("ZrdR")),y=Object(g.a)(f,function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("el-dialog",{staticClass:"tree_dialog",attrs:{title:"为角色分配权限",width:"60%","close-on-click-modal":!1,center:"",visible:e.visib,"before-close":e.closeDialog},on:{"update:visible":function(t){e.visib=t}}},[i("el-tree",{ref:"roleTree",staticClass:"el-tree",attrs:{data:e.treeData,"show-checkbox":"","node-key":"id","default-expand-all":"","default-checked-keys":e.checkedKeys}}),e._v(" "),i("div",{staticStyle:{"text-align":"center"},attrs:{slot:"footer"},slot:"footer"},[i("el-button",{attrs:{type:"primary",plain:""},on:{click:e.saveTreeData}},[e._v("保存")]),e._v(" "),i("el-button",{attrs:{type:"danger",plain:""},on:{click:e.closeDialog}},[e._v("取消")])],1)],1)},[],!1,null,"7d7da61c",null);y.options.__file="RoleAuthority.vue";var m={name:"AccessRole",components:{RoleAuthority:y.exports},data:function(){var e=this;return{dialogVisibleSetAuthorityForRole:!1,roleCode:"",crudOption:{title:"角色管理",labelWidth:"160px",tableButtons:[{label:"新增",type:"",permission:"roleManage:insert",icon:"el-icon-plus",plain:!0,click:function(){return e.$refs.listPage.handleOpenEditView("add")}},{label:"删除",type:"danger",permission:"roleManage:delete",icon:"el-icon-delete",plain:!1,click:function(){return e.$refs.listPage.handleDeleteBatch()}}],rowButtons:[{label:"编辑",permission:"roleManage:update",click:function(t){return e.$refs.listPage.handleOpenEditView("edit",t)}},{label:"分配权限",permission:"roleManage:grantAuthority",click:this.handleOpenDialogSetAuthorityForRole},{label:"删除",permission:"roleManage:delete",click:function(t){return e.$refs.listPage.handleDeleteBatch(t)}}],queryFormFields:[{inputType:"input",label:"角色编码",field:"roleCode"},{inputType:"input",label:"角色名称",field:"roleName"},{inputType:"anji-select",anjiSelectOption:{dictCode:"ENABLE_FLAG"},label:"启用状态",field:"enableFlag"}],buttons:{query:{api:r,permission:"roleManage:query"},queryByPrimarykey:{api:d,permission:"roleManage:query"},add:{api:l,permission:"roleManage:insert"},delete:{api:n,permission:"roleManage:delete"},edit:{api:o,permission:"roleManage:update"},rowButtonsWidth:140},columns:[{label:"",field:"id",primaryKey:!0,tableHide:!0,editHide:!0},{label:"角色编码",placeholder:"",field:"roleCode",editField:"roleCode",tableHide:!0,inputType:"input",rules:[{required:!0,message:"角色编码必填",trigger:"blur"},{min:1,max:32,message:"不超过32个字符",trigger:"blur"}],disabled:!1},{label:"角色名称",placeholder:"",field:"roleName",fieldTableRowRenderer:function(e){return e.roleName+"["+e.roleCode+"]"},editField:"roleName",inputType:"input",rules:[{required:!0,message:"角色名称必填",trigger:"blur"},{min:1,max:64,message:"不超过64个字符",trigger:"blur"}],disabled:!1},{label:"启用状态",placeholder:"",field:"enableFlag",fieldTableRowRenderer:function(t){return e.getDictLabelByCode("ENABLE_FLAG",t.enableFlag)},editField:"enableFlag",inputType:"anji-select",anjiSelectOption:{dictCode:"ENABLE_FLAG"},colorStyle:{0:"table-danger",1:"table-success"},rules:[{required:!0,message:"启用状态必填",trigger:"blur"}],disabled:!1},{label:"创建人",placeholder:"",field:"createBy",editField:"createBy",inputType:"input",editHide:"hideOnAdd",disabled:!0},{label:"创建时间",placeholder:"",field:"createTime",editField:"createTime",inputType:"input",editHide:"hideOnAdd",disabled:!0},{label:"修改人",placeholder:"",field:"updateBy",editField:"updateBy",inputType:"input",editHide:"hideOnAdd",disabled:!0},{label:"修改时间",placeholder:"",field:"updateTime",editField:"updateTime",inputType:"input",editHide:"hideOnAdd",disabled:!0}]}}},methods:{handleOpenDialogSetAuthorityForRole:function(e){this.roleCode=e.roleCode,this.dialogVisibleSetAuthorityForRole=!0}}},v=Object(g.a)(m,function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("anji-crud",{ref:"listPage",attrs:{option:e.crudOption},scopedSlots:e._u([{key:"pageSection",fn:function(){return[i("RoleAuthority",{attrs:{"role-code":e.roleCode,visib:e.dialogVisibleSetAuthorityForRole},on:{handleClose:function(t){e.dialogVisibleSetAuthorityForRole=!1}}})]},proxy:!0}])})},[],!1,null,null,null);v.options.__file="index.vue";t.default=v.exports}}]);