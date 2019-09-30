<template>
<view >
	<cover-view class="header">
		<view class="left"><fa type="search" size="26" color="#fff"></fa></view>
		<view class="center">
			<text class="on">推荐</text> 
			<text>深圳</text>
		</view>
		<view class="right"></view>
	</cover-view>
	<view id="list" @scroll="scroll" @scrolltolower="pushVideoList" :style="{height:listHeight}">
		<view v-for="(item, index) in list" :key="index" class="cell" :id="'item_'+index" :data-index="index">
			<view class="user">
				<image class="avatar" :src="item.user.avatar"></image>
				<text class="nickname">{{item.user.nickname}}</text>
				<fa type="ellipsis-h" class="more" size="32" color="#ff"></fa>
			</view>
			<view class="title">{{item.title}}</view>
			<videoplayer :src="item.src"
				@tap="videoplayerclick"
				:controls="true"
				:play="item.flag" v-if="Math.abs(current-index)<=1"
				:initialTime="item.initialTime" @pause="pauseVideo"
			>
			</videoplayer>
			<view class="info">
				<view>
					<fa type="heart" size="16" color="#f00" v-if="item.islike"></fa>
					<fa type="heart-o" size="16" color="#fff" v-else="item.islike"></fa>
					赞
				</view>
				<view>
					<fa type="commenting-o" size="16" color="#fff"></fa>
					评论
				</view>
				<view>
					<fa type="send" size="16" color="#fff"></fa>
					分享
				</view>
				<text class="date">09-09</text>
			</view>
			<view class="comment">
				<view class="zan">2048人赞过</view>
				<view class="item">
					<text class="nickname">DY鹏：</text>我一定是飘了,我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了
				</view>
				<view class="item">
					<text class="nickname">DY鹏：</text>我一定是飘了,我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了我一定是飘了
				</view>
				<view class="more">查看全部2048条评论</view>
				<view class="add">
					<fa type="pencil" size="12" color="#fff"></fa>
					添加评论
				</view>
			</view>
		</view>
	</view>
</view>
</template>

<script>
	import videoplayer from '@/components/videoplayer.vue'
    let observer = uni.createIntersectionObserver(this,{observeAll:true}).relativeToViewport({
		top:0,bottom:100,left:0,right:0
	});
	export default {
		components: { videoplayer},
		data() {
			return {
				list:[],
				current: 0,
				listHeight:0,
			}
		},
		async mounted() {
			let that = this;
			uni.getSystemInfo({
				success: function (res) {
					// #ifdef H5
					that.listHeight = (res.windowHeight-50)+'px';
					// #endif
					// #ifndef H5
					that.listHeight = (res.windowHeight-56)+'px';
					// #endif
				}
			});
			await this.pushVideoList()
			this.videoPlay(this.current)
		},
        onReady() {
        },
        onUnload() {
            if (observer) {
                observer.disconnect()
            }
        },
		methods: {
			videoPlay(current){
				let promise = new Promise((resolve,reject)=>{
					resolve()
				})
				current = current || this.current;
				promise.then(res=>{
					this.list[current].flag = !this.list[current].flag
				})
			},
			pauseVideo(val){
				//if(typeof this.list[this.current].initialTime !='undefined') this.list[this.current].initialTime = val
			},
			scroll(e){
				let that = this;
				observer = uni.createIntersectionObserver(this,{observeAll:true}).relativeToViewport({
					top:0,bottom:100,left:0,right:0
				}).relativeTo('#list').observe('.cell', (res) => {
					if(res.intersectionRatio>0.8 && that.current != res.dataset.index){
						that.list[that.current].flag = !that.list[that.current].flag
						that.current = res.dataset.index;
						that.videoPlay(res.dataset.index);
						if(that.list.length-that.current<2){
							that.pushVideoList();
						}
					}
				});
			},
			videoplayerclick(){
				
			},
			parseContent(str){
				//str = str.replace(/&quot;/g,function(){return '"';}).replace(/&lt;/g,function(){return '<';}).replace(/&gt;/g,function(){return '>';}).replace(/&amp;/g,function(){return '&';}).replace(/&nbsp;/g,function(){return ' ';});
				var c=str.match(new RegExp(/(?<=src=['"])([^'"=\s]+)/g));
				var d=str.match(new RegExp(/(?<=poster=['"])([^'"=\s]+)/g));
				console.log(c,d);
				return {src:c[0],poster:d[0]};
			},
			pushVideoList(){
				let that = this;
				let promise = new Promise((resolve,reject)=>{
					try{
						uni.get('/index/Douyin/getVideoList.html',function(res){
							var videoGroup =  Array.from(res.data);
							for (let i=0;i<videoGroup.length;i++) {
								videoGroup[i]['islike'] = false;
								var r = that.parseContent(videoGroup[i].content);
								videoGroup[i]['src'] = r.src;
								videoGroup[i]['poster'] = r.poster;
							}
							that.list = [...that.list,...videoGroup]
							//console.log(that.list);
							resolve()
						});						
					}catch(e){
						console.log(e);
					}
				});
				return promise
			},
		}
	}
</script>

<style>
	.header{position: sticky;}
	.item_container{overflow:scroll;}
	#list{overflow-y: scroll;}
	.cell{padding:20rpx;margin: 20rpx 0;border-bottom: 1px solid #21252d;}
	.cell .user{position: relative;display: flex;flex-direction: row;align-items: center;padding: 20rpx 0;}
	.cell .user .avatar{width: 68rpx;height: 68rpx;border-radius:50%;}
	.cell .user .nickname{line-height: 68rpx;padding: 0 20rpx;}
	.cell .user .more{position: absolute;right: 20rpx;}
	.cell .title{font-size: 14px;margin-bottom: 20rpx;}
	.cell .video{}
	.cell .info{padding: 20rpx 0;font-size: 14px;position: relative;display: flex;flex-direction: row-reverse;align-items: center;}
	.cell .info uni-view{padding: 0 10rpx;}
	.cell .info .date{position: absolute;left: 0rpx;color:#838389;}
	.cell .comment{font-size:14px;background: #1c1e2a;color: #ccc;padding: 20rpx;border-radius: 2px;margin:20rpx 0;}
	.comment .zan{font-size: 12px;padding:5px 0 10px 0;border-bottom: 1px solid #5a5858;}
	.comment .item{margin: 20rpx 0;}
	.comment .item .nickname{font-weight: 700;}
	.comment .more{font-size: 12px;color:#8e9198;}
	.comment .add{font-size: 12px;color:#8e9198;margin: 10px 0;}
	.comment .add view{margin: 0 10px 0 0;}

</style>
