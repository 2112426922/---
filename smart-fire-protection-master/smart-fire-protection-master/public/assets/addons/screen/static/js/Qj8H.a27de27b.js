(window.webpackJsonp=window.webpackJsonp||[]).push([["Qj8H"],{Qj8H:function(e,t,a){"use strict";a.r(t);var i=a("cLjf"),r=a.n(i),n=a("hDQ3"),l=a.n(n),s=a("t3Un");function o(e){return Object(s.a)({url:"accessUser/pageList",method:"GET",params:e})}function d(e){return Object(s.a)({url:"accessUser",method:"post",data:e})}function u(e){return Object(s.a)({url:"accessUser/delete/batch",method:"post",data:e})}function c(e){return Object(s.a)({url:"accessUser",method:"put",data:e})}function p(e){return Object(s.a)({url:"accessUser/"+e.id,method:"get",params:{accessKey:e.accessKey}})}function g(e){return Object(s.a)({url:"accessUser/roleTree/"+e,method:"get"})}function m(e){return Object(s.a)({url:"accessUser/saveRoleTree",method:"post",data:e})}function b(e){return Object(s.a)({url:"accessUser/resetPassword",method:"post",data:e})}var f={props:{visib:{required:!0,type:Boolean,default:!1},loginName:{required:!0,type:String,default:function(){return""}}},data:function(){return{checkedKeys:[],treeData:[]}},watch:{visib:function(e){e&&this.getTreeData()}},created:function(){},methods:{getTreeData:function(){var e=this;return l()(r.a.mark(function t(){var a,i,n;return r.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,g(e.loginName);case 2:if(a=t.sent,i=a.code,n=a.data,"200"==i){t.next=7;break}return t.abrupt("return");case 7:e.treeData=n.treeData,e.checkedKeys=n.checkedKeys;case 9:case"end":return t.stop()}},t,e)}))()},saveTreeData:function(){var e=this;return l()(r.a.mark(function t(){var a,i;return r.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return a={loginName:e.loginName,roleCodeList:e.$refs.roleTree.getCheckedKeys(!0)},t.next=3,m(a);case 3:if(i=t.sent,"200"==i.code){t.next=7;break}return t.abrupt("return");case 7:e.closeDialog();case 8:case"end":return t.stop()}},t,e)}))()},closeDialog:function(){this.treeData=[],this.checkedKeys=[],this.$emit("handleClose")}}},h=a("ZrdR"),y=Object(h.a)(f,function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("el-dialog",{staticClass:"tree_dialog",attrs:{title:"为用户分配角色",width:"60%","close-on-click-modal":!1,center:"",visible:e.visib,"before-close":e.closeDialog},on:{"update:visible":function(t){e.visib=t}}},[a("el-tree",{ref:"roleTree",attrs:{data:e.treeData,"show-checkbox":"","node-key":"id","default-expand-all":"","default-checked-keys":e.checkedKeys}}),e._v(" "),a("div",{staticStyle:{"text-align":"center"},attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{type:"primary",plain:""},on:{click:e.saveTreeData}},[e._v("保存")]),e._v(" "),a("el-button",{attrs:{type:"danger",plain:""},on:{click:e.closeDialog}},[e._v("取消")])],1)],1)},[],!1,null,null,null);y.options.__file="UserRole.vue";var T={name:"AccessUser",components:{UserRole:y.exports},data:function(){var e=this;return{dialogVisibleSetRoleForUser:!1,loginName:"",crudOption:{title:"用户管理",labelWidth:"140px",queryFormFields:[{inputType:"anji-select",anjiSelectOption:{dictCode:"ENABLE_FLAG"},label:"启用状态",field:"enableFlag"},{inputType:"input",label:"登录名",field:"loginName"},{inputType:"input",label:"真实姓名",field:"realName"},{inputType:"input",label:"手机号",field:"phone"}],tableButtons:[{label:"新增",type:"roleManage:insert",permission:"userManage:insert",icon:"el-icon-plus",plain:!0,click:function(){return e.$refs.listPage.handleOpenEditView("add")}},{label:"删除",type:"danger",permission:"userManage:delete",icon:"el-icon-delete",plain:!1,click:function(){return e.$refs.listPage.handleDeleteBatch()}}],rowButtons:[{label:"编辑",permission:"userManage:update",click:function(t){return e.$refs.listPage.handleOpenEditView("edit",t)}},{label:"分配权限",permission:"userManage:grantRole",click:this.handleOpenDialogSetRoleForUser},{label:"重置密码",permission:"userManage:resetPassword",click:this.resetPassword},{label:"删除",permission:"userManage:delete",click:function(t){return e.$refs.listPage.handleDeleteBatch(t)}}],buttons:{query:{api:o,permission:"userManage:query"},queryByPrimarykey:{api:p,permission:"userManage:query"},add:{api:d,permission:"userManage:insert"},delete:{api:u,permission:"userManage:delete"},edit:{api:c,permission:"userManage:update"},rowButtonsWidth:150},columns:[{label:"",field:"id",primaryKey:!0,tableHide:!0,editHide:!0},{label:"登录名",placeholder:"",field:"loginName",tableHide:!0,editField:"loginName",inputType:"input",rules:[{required:!0,message:"登录名必填",trigger:"blur"},{min:1,max:64,message:"不超过64个字符",trigger:"blur"}],disabled:!1},{label:"真实姓名",placeholder:"",field:"realName",fieldTableRowRenderer:function(e){return e.realName+"["+e.loginName+"]"},editField:"realName",inputType:"input",rules:[{required:!0,message:"真实姓名必填",trigger:"blur"},{min:1,max:64,message:"不超过64个字符",trigger:"blur"}],disabled:!1},{label:"手机号码",placeholder:"",field:"phone",editField:"phone",inputType:"input",rules:[{min:1,max:16,message:"不超过16个字符",trigger:"blur"}],disabled:!1},{label:"用户邮箱",placeholder:"",field:"email",editField:"email",inputType:"input",rules:[{min:1,max:64,message:"不超过64个字符",trigger:"blur"}],disabled:!1},{label:"备注",placeholder:"",field:"remark",editField:"remark",inputType:"input",rules:[{min:1,max:512,message:"不超过512个字符",trigger:"blur"}],disabled:!1},{label:"启用状态",placeholder:"",field:"enableFlag",fieldTableRowRenderer:function(t){return e.getDictLabelByCode("ENABLE_FLAG",t.enableFlag)},editField:"enableFlag",inputType:"anji-select",anjiSelectOption:{dictCode:"ENABLE_FLAG"},colorStyle:{0:"table-danger",1:"table-success"},rules:[{required:!0,message:"启用状态必填",trigger:"blur"}],disabled:!1},{label:"最后一次登陆时间",placeholder:"",field:"lastLoginTime",editField:"lastLoginTime",inputType:"input",rules:[],disabled:!0},{label:"最后一次登录IP",placeholder:"",field:"lastLoginIp",editField:"lastLoginIp",inputType:"input",rules:[{min:1,max:16,message:"不超过16个字符",trigger:"blur"}],disabled:!0},{label:"创建人",placeholder:"",field:"createBy",columnType:"expand",editField:"createBy",inputType:"input",editHide:"hideOnAdd",disabled:!0},{label:"创建时间",placeholder:"",field:"createTime",columnType:"expand",editField:"createTime",inputType:"input",editHide:"hideOnAdd",disabled:!0},{label:"修改人",placeholder:"",field:"updateBy",columnType:"expand",editField:"updateBy",inputType:"input",editHide:"hideOnAdd",disabled:!0},{label:"修改时间",placeholder:"",field:"updateTime",columnType:"expand",editField:"updateTime",inputType:"input",editHide:"hideOnAdd",disabled:!0}]}}},methods:{handleOpenDialogSetRoleForUser:function(e){this.loginName=e.loginName,this.dialogVisibleSetRoleForUser=!0},resetPassword:function(e){var t=this;this.$confirm("重置密码, 是否继续?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(l()(r.a.mark(function a(){var i;return r.a.wrap(function(a){for(;;)switch(a.prev=a.next){case 0:return a.next=2,b({loginName:e.loginName});case 2:if(i=a.sent,"200"==i.code){a.next=6;break}return a.abrupt("return");case 6:t.$message({type:"success",message:"重置成功!"});case 7:case"end":return a.stop()}},a,t)}))).catch(function(){t.$message({type:"info",message:"已取消重置"})})}}},v=Object(h.a)(T,function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("anji-crud",{ref:"listPage",attrs:{option:e.crudOption},scopedSlots:e._u([{key:"pageSection",fn:function(){return[a("UserRole",{attrs:{"login-name":e.loginName,visib:e.dialogVisibleSetRoleForUser},on:{handleClose:function(t){e.dialogVisibleSetRoleForUser=!1}}})]},proxy:!0}])})},[],!1,null,null,null);v.options.__file="index.vue";t.default=v.exports}}]);