(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-dcfb"],{"3Xui":function(e,t,i){"use strict";i.r(t);var s=i("EPZ6"),r=i.n(s),o=i("cLjf"),n=i.n(o),a=i("hDQ3"),c=i.n(a),h=i("omC7"),p=i.n(h),d=i("6Cps"),l=i.n(d);function u(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"XwKsGlMcdPMEhR1B",i=l.a.enc.Utf8.parse(t),s=l.a.enc.Utf8.parse(e);return l.a.AES.encrypt(s,i,{mode:l.a.mode.ECB,padding:l.a.pad.Pkcs7}).toString()}function f(e){var t=e.$el.parentNode.offsetWidth||window.offsetWidth,i=e.$el.parentNode.offsetHeight||window.offsetHeight;return{imgWidth:-1!=e.imgSize.width.indexOf("%")?parseInt(this.imgSize.width)/100*t+"px":this.imgSize.width,imgHeight:-1!=e.imgSize.height.indexOf("%")?parseInt(this.imgSize.height)/100*i+"px":this.imgSize.height,barWidth:-1!=e.barSize.width.indexOf("%")?parseInt(this.barSize.width)/100*t+"px":this.barSize.width,barHeight:-1!=e.barSize.height.indexOf("%")?parseInt(this.barSize.height)/100*i+"px":this.barSize.height}}var g=i("Asgo"),m=i.n(g),v=i("f0Pt"),y=i.n(v),b=i("Enfz");y.a.defaults.baseURL=FACONFIG.BASE_API;var w=y.a.create({withCredentials:!1,timeout:6e4,headers:{"Content-Type":"application/json"}});w.interceptors.request.use(function(e){return e},function(e){m.a.reject(e)}),w.interceptors.response.use(function(e){var t=e.data;return 200==t.code?t:"50014"!=t.code?"3100"==t.code||"3101"==t.code?t:(Object(b.Message)({message:t.repMsg||t.message||t.msg,type:"error",duration:3e3}),t):void Object(b.MessageBox)({message:"当前登录已失效或异地登录，请重新登录",type:"error",duration:3e3}).then(function(){sessionStorage.clear(),localStorage.clear(),window.location.href="/"}).catch(function(e){})},function(e){var t=e.response.code,i=e.response.data,s="";s=200!=t?"服务器内部错误，请联系管理员":"失败原因："+i.code+"--"+i.repMsg,Object(b.Message)({message:s,type:"error",duration:5e3})});var k=w;function S(e){return k({url:"auth-service/user/captcha/get",method:"post",data:e})}function x(e){return k({url:"auth-service/user/captcha/check",method:"post",data:e})}var C={name:"VerifySlide",props:{captchaType:{type:String},type:{type:String,default:"1"},mode:{type:String,default:"fixed"},vSpace:{type:Number,default:5},explain:{type:String,default:"向右滑动完成验证"},imgSize:{type:Object,default:function(){return{width:"310px",height:"155px"}}},blockSize:{type:Object,default:function(){return{width:"50px",height:"50px"}}},barSize:{type:Object,default:function(){return{width:"310px",height:"40px"}}}},data:function(){return{secretKey:"",passFlag:"",backImgBase:"",blockBackImgBase:"",backToken:"",startMoveTime:"",endMovetime:"",tipsBackColor:"",tipWords:"",text:"",finishText:"",setSize:{imgHeight:0,imgWidth:0,barHeight:0,barWidth:0},top:0,left:0,moveBlockLeft:void 0,leftBarWidth:void 0,moveBlockBackgroundColor:void 0,leftBarBorderColor:"#ddd",iconColor:void 0,iconClass:"icon-right",status:!1,isEnd:!1,showRefresh:!0,transitionLeft:"",transitionWidth:""}},computed:{barArea:function(){return this.$el.querySelector(".verify-bar-area")},resetSize:function(){return f}},methods:{init:function(){var e=this;this.text=this.explain,this.getPictrue(),this.$nextTick(function(){var t=e.resetSize(e);for(var i in t)e.$set(e.setSize,i,t[i]);e.$parent.$emit("ready",e)});var t=this;window.removeEventListener("touchmove",function(e){t.move(e)}),window.removeEventListener("mousemove",function(e){t.move(e)}),window.removeEventListener("touchend",function(){t.end()}),window.removeEventListener("mouseup",function(){t.end()}),window.addEventListener("touchmove",function(e){t.move(e)}),window.addEventListener("mousemove",function(e){t.move(e)}),window.addEventListener("touchend",function(){t.end()}),window.addEventListener("mouseup",function(){t.end()})},start:function(e){if((e=e||window.event).touches)t=e.touches[0].pageX;else var t=e.clientX;this.startLeft=Math.floor(t-this.barArea.getBoundingClientRect().left),this.startMoveTime=+new Date,0==this.isEnd&&(this.text="",this.moveBlockBackgroundColor="#337ab7",this.leftBarBorderColor="#337AB7",this.iconColor="#fff",e.stopPropagation(),this.status=!0)},move:function(e){if(e=e||window.event,this.status&&0==this.isEnd){if(e.touches)t=e.touches[0].pageX;else var t=e.clientX;var i=t-this.barArea.getBoundingClientRect().left;i>=this.barArea.offsetWidth-parseInt(parseInt(this.blockSize.width)/2)-2&&(i=this.barArea.offsetWidth-parseInt(parseInt(this.blockSize.width)/2)-2),i<=0&&(i=parseInt(parseInt(this.blockSize.width)/2)),this.moveBlockLeft=i-this.startLeft+"px",this.leftBarWidth=i-this.startLeft+"px"}},end:function(){var e=this;this.endMovetime=+new Date;var t=this;if(this.status&&0==this.isEnd){var i=parseInt((this.moveBlockLeft||"").replace("px",""));i=310*i/parseInt(this.setSize.imgWidth),x({captchaType:this.captchaType,pointJson:this.secretKey?u(p()({x:i,y:5}),this.secretKey):p()({x:i,y:5}),token:this.backToken}).then(function(s){if("0000"==s.repCode){e.moveBlockBackgroundColor="#5cb85c",e.leftBarBorderColor="#5cb85c",e.iconColor="#fff",e.iconClass="icon-check",e.showRefresh=!1,e.isEnd=!0,"pop"==e.mode&&setTimeout(function(){e.$parent.clickShow=!1,e.refresh()},1500),e.passFlag=!0,e.tipWords=((e.endMovetime-e.startMoveTime)/1e3).toFixed(2)+"s验证成功";var r=e.secretKey?u(e.backToken+"---"+p()({x:i,y:5}),e.secretKey):e.backToken+"---"+p()({x:i,y:5});e.$parent.$emit("success",{captchaVerification:r}),setTimeout(function(){e.tipWords="",e.$parent.closeBox()},1e3)}else e.moveBlockBackgroundColor="#d9534f",e.leftBarBorderColor="#d9534f",e.iconColor="#fff",e.iconClass="icon-close",e.passFlag=!1,setTimeout(function(){t.refresh()},1e3),e.$parent.$emit("error",e),e.tipWords="验证失败",setTimeout(function(){e.tipWords=""},1e3)}),this.status=!1}},refresh:function(){var e=this;this.showRefresh=!0,this.finishText="",this.transitionLeft="left .3s",this.moveBlockLeft=0,this.leftBarWidth=void 0,this.transitionWidth="width .3s",this.leftBarBorderColor="#ddd",this.moveBlockBackgroundColor="#fff",this.iconColor="#000",this.iconClass="icon-right",this.isEnd=!1,this.getPictrue(),setTimeout(function(){e.transitionWidth="",e.transitionLeft="",e.text=e.explain},300)},getPictrue:function(){var e=this;S({captchaType:this.captchaType}).then(function(t){"0000"==t.repCode?(e.backImgBase=t.repData.originalImageBase64,e.blockBackImgBase=t.repData.jigsawImageBase64,e.backToken=t.repData.token,e.secretKey=t.repData.secretKey):e.tipWords=t.repMsg})}},watch:{type:{immediate:!0,handler:function(){this.init()}}},mounted:function(){this.$el.onselectstart=function(){return!1}}},B=i("ZrdR"),z=Object(B.a)(C,function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticStyle:{position:"relative"}},["2"===e.type?i("div",{staticClass:"verify-img-out",style:{height:parseInt(e.setSize.imgHeight)+e.vSpace+"px"}},[i("div",{staticClass:"verify-img-panel",style:{width:e.setSize.imgWidth,height:e.setSize.imgHeight}},[i("img",{staticStyle:{width:"100%",height:"100%",display:"block"},attrs:{src:"data:image/png;base64,"+e.backImgBase,alt:""}}),e._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:e.showRefresh,expression:"showRefresh"}],staticClass:"verify-refresh",on:{click:e.refresh}},[i("i",{staticClass:"iconfont icon-refresh"})]),e._v(" "),i("transition",{attrs:{name:"tips"}},[e.tipWords?i("span",{staticClass:"verify-tips",class:e.passFlag?"suc-bg":"err-bg"},[e._v(e._s(e.tipWords))]):e._e()])],1)]):e._e(),e._v(" "),i("div",{staticClass:"verify-bar-area",style:{width:e.setSize.imgWidth,height:e.barSize.height,"line-height":e.barSize.height}},[i("span",{staticClass:"verify-msg",domProps:{textContent:e._s(e.text)}}),e._v(" "),i("div",{staticClass:"verify-left-bar",style:{width:void 0!==e.leftBarWidth?e.leftBarWidth:e.barSize.height,height:e.barSize.height,"border-color":e.leftBarBorderColor,transaction:e.transitionWidth}},[i("span",{staticClass:"verify-msg",domProps:{textContent:e._s(e.finishText)}}),e._v(" "),i("div",{staticClass:"verify-move-block",style:{width:e.barSize.height,height:e.barSize.height,"background-color":e.moveBlockBackgroundColor,left:e.moveBlockLeft,transition:e.transitionLeft},on:{touchstart:e.start,mousedown:e.start}},[i("i",{class:["verify-icon iconfont",e.iconClass],style:{color:e.iconColor}}),e._v(" "),"2"===e.type?i("div",{staticClass:"verify-sub-block",style:{width:Math.floor(47*parseInt(e.setSize.imgWidth)/310)+"px",height:e.setSize.imgHeight,top:"-"+(parseInt(e.setSize.imgHeight)+e.vSpace)+"px","background-size":e.setSize.imgWidth+" "+e.setSize.imgHeight}},[i("img",{staticStyle:{width:"100%",height:"100%",display:"block"},attrs:{src:"data:image/png;base64,"+e.blockBackImgBase,alt:""}})]):e._e()])])])])},[],!1,null,null,null);z.options.__file="VerifySlide.vue";var T=z.exports,P=i("6ZY3"),_=i.n(P),$={name:"VerifyPoints",props:{mode:{type:String,default:"fixed"},captchaType:{type:String},vSpace:{type:Number,default:5},imgSize:{type:Object,default:function(){return{width:"310px",height:"155px"}}},barSize:{type:Object,default:function(){return{width:"310px",height:"40px"}}}},data:function(){return{secretKey:"",checkNum:3,fontPos:[],checkPosArr:[],num:1,pointBackImgBase:"",poinTextList:[],backToken:"",setSize:{imgHeight:0,imgWidth:0,barHeight:0,barWidth:0},tempPoints:[],text:"",barAreaColor:void 0,barAreaBorderColor:void 0,showRefresh:!0,bindingClick:!0}},computed:{resetSize:function(){return f}},methods:{init:function(){var e=this;this.fontPos.splice(0,this.fontPos.length),this.checkPosArr.splice(0,this.checkPosArr.length),this.num=1,this.getPictrue(),this.$nextTick(function(){e.setSize=e.resetSize(e),e.$parent.$emit("ready",e)})},canvasClick:function(e){var t=this;this.checkPosArr.push(this.getMousePos(this.$refs.canvas,e)),this.num==this.checkNum&&(this.num=this.createPoint(this.getMousePos(this.$refs.canvas,e)),this.checkPosArr=this.pointTransfrom(this.checkPosArr,this.setSize),setTimeout(function(){var e=t.secretKey?u(t.backToken+"---"+p()(t.checkPosArr),t.secretKey):t.backToken+"---"+p()(t.checkPosArr);x({captchaType:t.captchaType,pointJson:t.secretKey?u(p()(t.checkPosArr),t.secretKey):p()(t.checkPosArr),token:t.backToken}).then(function(i){"0000"==i.repCode?(t.barAreaColor="#4cae4c",t.barAreaBorderColor="#5cb85c",t.text="验证成功",t.bindingClick=!1,"pop"==t.mode&&setTimeout(function(){t.$parent.clickShow=!1,t.refresh()},1500),t.$parent.$emit("success",{captchaVerification:e})):(t.$parent.$emit("error",t),t.barAreaColor="#d9534f",t.barAreaBorderColor="#d9534f",t.text="验证失败",setTimeout(function(){t.refresh()},700))})},400)),this.num<this.checkNum&&(this.num=this.createPoint(this.getMousePos(this.$refs.canvas,e)))},getMousePos:function(e,t){return{x:t.offsetX,y:t.offsetY}},createPoint:function(e){return this.tempPoints.push(_()({},e)),++this.num},refresh:function(){this.tempPoints.splice(0,this.tempPoints.length),this.barAreaColor="#000",this.barAreaBorderColor="#ddd",this.bindingClick=!0,this.fontPos.splice(0,this.fontPos.length),this.checkPosArr.splice(0,this.checkPosArr.length),this.num=1,this.getPictrue(),this.text="验证失败",this.showRefresh=!0},getPictrue:function(){var e=this;S({captchaType:this.captchaType}).then(function(t){"0000"==t.repCode?(e.pointBackImgBase=t.repData.originalImageBase64,e.backToken=t.repData.token,e.secretKey=t.repData.secretKey,e.poinTextList=t.repData.wordList,e.text="请依次点击【"+e.poinTextList.join(",")+"】"):e.text=t.repMsg})},pointTransfrom:function(e,t){return e.map(function(e){return{x:Math.round(310*e.x/parseInt(t.imgWidth)),y:Math.round(155*e.y/parseInt(t.imgHeight))}})}},watch:{type:{immediate:!0,handler:function(){this.init()}}},mounted:function(){this.$el.onselectstart=function(){return!1}}},A=Object(B.a)($,function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticStyle:{position:"relative"}},[i("div",{staticClass:"verify-img-out"},[i("div",{staticClass:"verify-img-panel",style:{width:e.setSize.imgWidth,height:e.setSize.imgHeight,"background-size":e.setSize.imgWidth+" "+e.setSize.imgHeight,"margin-bottom":e.vSpace+"px"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:e.showRefresh,expression:"showRefresh"}],staticClass:"verify-refresh",staticStyle:{"z-index":"3"},on:{click:e.refresh}},[i("i",{staticClass:"iconfont icon-refresh"})]),e._v(" "),i("img",{ref:"canvas",staticStyle:{width:"100%",height:"100%",display:"block"},attrs:{src:"data:image/png;base64,"+e.pointBackImgBase,alt:""},on:{click:function(t){e.bindingClick&&e.canvasClick(t)}}}),e._v(" "),e._l(e.tempPoints,function(t,s){return i("div",{key:s,staticClass:"point-area",style:{"background-color":"#1abd6c",color:"#fff","z-index":9999,width:"20px",height:"20px","text-align":"center","line-height":"20px","border-radius":"50%",position:"absolute",top:parseInt(t.y-10)+"px",left:parseInt(t.x-10)+"px"}},[e._v("\n                "+e._s(s+1)+"\n            ")])})],2)]),e._v(" "),i("div",{staticClass:"verify-bar-area",style:{width:e.setSize.imgWidth,color:this.barAreaColor,"border-color":this.barAreaBorderColor,"line-height":this.barSize.height}},[i("span",{staticClass:"verify-msg"},[e._v(e._s(e.text))])])])},[],!1,null,null,null);A.options.__file="VerifyPoints.vue";var W=A.exports,I={name:"Vue2Verify",props:{locale:{require:!1,type:String,default:function(){if(navigator.language)var e=navigator.language;else e=navigator.browserLanguage;return e}},captchaType:{type:String,required:!0},figure:{type:Number},arith:{type:Number},mode:{type:String,default:"pop"},vSpace:{type:Number},explain:{type:String},imgSize:{type:Object,default:function(){return{width:"310px",height:"155px"}}},blockSize:{type:Object},barSize:{type:Object}},data:function(){return{clickShow:!1,verifyType:void 0,componentType:void 0}},methods:{i18n:function(e){if(this.$t)return this.$t(e);var t=this.$options.i18n.messages[this.locale]||this.$options.i18n.messages["en-US"];return t[e]},refresh:function(){this.instance.refresh&&this.instance.refresh()},closeBox:function(){this.clickShow=!1,this.refresh()},show:function(){"pop"==this.mode&&(this.clickShow=!0)}},computed:{instance:function(){return this.$refs.instance||{}},showBox:function(){return"pop"!=this.mode||this.clickShow}},watch:{captchaType:{immediate:!0,handler:function(e){switch(e.toString()){case"blockPuzzle":this.verifyType="2",this.componentType="VerifySlide";break;case"clickWord":this.verifyType="",this.componentType="VerifyPoints"}}}},components:{VerifySlide:T,VerifyPoints:W}},L=(i("q8np"),Object(B.a)(I,function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{directives:[{name:"show",rawName:"v-show",value:e.showBox,expression:"showBox"}],class:"pop"==e.mode?"mask":""},[i("div",{class:"pop"==e.mode?"verifybox":"",style:{"max-width":parseInt(e.imgSize.width)+30+"px"}},["pop"==e.mode?i("div",{staticClass:"verifybox-top"},[e._v("\n            请完成安全验证\n            "),i("span",{staticClass:"verifybox-close",on:{click:e.closeBox}},[i("i",{staticClass:"iconfont icon-close"})])]):e._e(),e._v(" "),i("div",{staticClass:"verifybox-bottom",style:{padding:"pop"==e.mode?"15px":"0"}},[e.componentType?i(e.componentType,{ref:"instance",tag:"components",attrs:{captchaType:e.captchaType,type:e.verifyType,figure:e.figure,arith:e.arith,mode:e.mode,vSpace:e.vSpace,explain:e.explain,imgSize:e.imgSize,blockSize:e.blockSize,barSize:e.barSize}}):e._e()],1)])])},[],!1,null,null,null));L.options.__file="Verify.vue";var j=L.exports,O=i("jJIE"),E=i.n(O),M=i("7Qib"),F=i("dVUk"),N=i("sxct"),V=i("X4fA"),R={name:"Login",components:{Verify:j},data:function(){return{activeTop:"-50%",rememberPsw:!1,loginForm:{loginName:"",password:"",verifyCode:""},loginRules:{loginName:[{required:!0,message:"用户名必填",trigger:"blur"}],password:[{required:!0,message:"用户密码必填",trigger:"blur"}]},passwordType:"password",capsTooltip:!1,loading:!1,redirect:void 0,otherQuery:{},needCaptcha:!1,centerDialogVisible:!1}},watch:{$route:{handler:function(e){var t=e.query;t&&(this.redirect=t.redirect,this.otherQuery=this.getOtherQuery(t))},immediate:!0}},mounted:function(){this.handleLoginFocus()},methods:{handleLoginFocus:function(){""===this.loginForm.loginName?this.$refs.loginName.focus():""===this.loginForm.password&&this.$refs.password.focus()},getPsw:function(){var e=E.a.get("u_"+this.loginForm.loginName);this.loginForm.password=e&&Object(M.a)(e)},setTop:function(e){this.activeTop=e},checkCapslock:function(e){var t=e.key;this.capsTooltip=t&&1===t.length&&t>="A"&&t<="Z"},showPwd:function(){var e=this;"password"===this.passwordType?this.passwordType="":this.passwordType="password",this.$nextTick(function(){e.$refs.password.focus()})},useVerify:function(){var e=this;this.$refs.loginForm.validate(function(t){if(!t)return!1;e.$refs.verify.show()})},verifylogin:function(e){this.loginForm.verifyCode=e.captchaVerification,this.loginForm.verifyCode&&this.loginApi()},handleLogin:function(){var e=this;this.$refs.loginForm.validate(function(t){if(!t)return!1;e.loading=!0,e.needCaptcha?e.useVerify():e.loginApi()})},loginApi:function(){var e=this;return c()(n.a.mark(function t(){var i,s,r,o;return n.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return i={loginName:e.loginForm.loginName,password:Object(N.a)(e.loginForm.password),verifyCode:""},t.next=3,Object(F.a)(i);case 3:if(s=t.sent,r=s.code,o=s.data,e.loading=!1,"200"==r){t.next=9;break}return t.abrupt("return");case 9:Object(V.i)(o.token),Object(V.g)(o),e.rememberPsw&&E.a.set("u_"+e.loginForm.loginName,Object(M.b)(e.loginForm.password),{expires:15}),o&&o.captcha?e.needCaptcha=!0:(e.needCaptcha=!1,e.$router.push({path:e.redirect||"/index",query:e.otherQuery}));case 13:case"end":return t.stop()}},t,e)}))()},getOtherQuery:function(e){return r()(e).reduce(function(t,i){return"redirect"!==i&&(t[i]=e[i]),t},{})}}},H=(i("Ruor"),Object(B.a)(R,function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"login_container"},[t("el-alert",{attrs:{title:"页面未找到",type:"error",center:"","show-icon":""}})],1)},[],!1,null,null,null));H.options.__file="login.vue";t.default=H.exports},Ruor:function(e,t,i){"use strict";var s=i("udjw");i.n(s).a},"ksq+":function(e,t,i){},q8np:function(e,t,i){"use strict";var s=i("ksq+");i.n(s).a},udjw:function(e,t,i){}}]);