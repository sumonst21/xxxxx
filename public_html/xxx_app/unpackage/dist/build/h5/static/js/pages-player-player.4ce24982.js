(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-player-player"],{"2e0c":function(n,t,e){"use strict";var i=function(){var n=this,t=n.$createElement,e=n._self._c||t;return e("v-uni-view",[e("v-uni-view",{staticClass:"uni-padding-wrap uni-common-mt"},[e("v-uni-view",[e("v-uni-video",{attrs:{id:"myVideo",src:"https://cloud.com-youku-com.com/2724/hls/index.m3u8","danmu-list":n.danmuList,"enable-danmu":"","danmu-btn":"",controls:""},on:{error:function(t){t=n.$handleEvent(t),n.videoErrorCallback(t)}}})],1),e("v-uni-view",{staticClass:"uni-list uni-common-mt"},[e("v-uni-view",{staticClass:"uni-list-cell"},[e("v-uni-view",[e("v-uni-view",{staticClass:"uni-label"},[n._v("弹幕内容")])],1),e("v-uni-view",{staticClass:"uni-list-cell-db"},[e("v-uni-input",{staticClass:"uni-input",attrs:{type:"text",placeholder:"在此处输入弹幕内容"},model:{value:n.danmuValue,callback:function(t){n.danmuValue=t},expression:"danmuValue"}})],1)],1)],1),e("v-uni-view",{staticClass:"uni-btn-v"},[e("v-uni-button",{staticClass:"page-body-button",on:{click:function(t){t=n.$handleEvent(t),n.sendDanmu(t)}}},[n._v("发送弹幕")])],1)],1)],1)},a=[];e.d(t,"a",function(){return i}),e.d(t,"b",function(){return a})},4294:function(n,t,e){"use strict";e.r(t);var i=e("2e0c"),a=e("d804");for(var u in a)"default"!==u&&function(n){e.d(t,n,function(){return a[n]})}(u);var o=e("2877"),s=Object(o["a"])(a["default"],i["a"],i["b"],!1,null,"caa8374e",null);t["default"]=s.exports},"822e":function(n,t,e){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i={data:function(){return{src:"",danmuList:[{text:"第 1s 出现的弹幕",color:"#ff0000",time:1},{text:"第 3s 出现的弹幕",color:"#ff00ff",time:3}],danmuValue:""}},onReady:function(n){this.videoContext=uni.createVideoContext("myVideo")},methods:{sendDanmu:function(){this.videoContext.sendDanmu({text:this.danmuValue,color:this.getRandomColor()}),this.danmuValue=""},videoErrorCallback:function(n){uni.showModal({content:n.target.errMsg,showCancel:!1})},getRandomColor:function(){for(var n=[],t=0;t<3;++t){var e=Math.floor(256*Math.random()).toString(16);e=1==e.length?"0"+e:e,n.push(e)}return"#"+n.join("")}}};t.default=i},d804:function(n,t,e){"use strict";e.r(t);var i=e("822e"),a=e.n(i);for(var u in i)"default"!==u&&function(n){e.d(t,n,function(){return i[n]})}(u);t["default"]=a.a}}]);