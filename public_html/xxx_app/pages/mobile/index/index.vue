<template>
	<view class="body">
		<cover-view class="header">
			<view class="left"><fa type="search" size="26" color="#fff"></fa></view>
			<view class="center">
				<text class="on">推荐</text> 
				<text>附近</text>
			</view>
			<view class="right"></view>
		</cover-view>
		
        <!--vant van-swipe 滑动组件 -->
        <swiper :show-indicators="false" @change="changeCurrent" :current="current" vertical :loop="false" class="swiper">
            <swiper-item v-for="(item, index) in list" :key="index">
				<videoplayer :src="item.src" :poster="item.poster"
					@tap="videoplayerclick"
					:play="item.flag" v-if="Math.abs(current-index)<=1"
					:initialTime="item.initialTime" @pause="pauseVideo"
				>
				</videoplayer>
				<cover-view class="big_play" v-show="!item.flag" @click="playvideo"><fa type="play" size="40" color="#ffffff"/></cover-view>
                <!-- 底部作品描述 -->
				<cover-view class="post">
					<view class="user">@{{item.user.username}}</view>
					<view class="title">{{item.title}}</view>
				</cover-view>
                <!-- 右侧点赞、分享功能 -->
				<Operation :avatar="item.user.avatar" :islike="item.islike" @avatarTap="avatarTap" @likeTap="likeTap" @commentTap="commentTap" @messageTap="messageTap" @musicTap="musicTap"/>
            </swiper-item>
        </swiper>
	</view>
</template>

<script>
	import videoplayer from '@/components/videoplayer.vue'
	import Operation from '@/components/operation.vue';
	export default {
		components: {
			videoplayer,Operation
		},
		data() {
            let u = navigator.userAgent;
			return {
				list:[],
				current: 0,
			}
		},
		created(){
			//#ifdef APP-PLUS
			plus.screen.lockOrientation("portrait-primary")
			//#endif
		},
		async mounted() {
			await this.pushVideoList()
			this.videoPlay(this.current)
		},
		onHide(){
			for (let item of this.list) {
				item.flag = false
			}
		},
		methods: {
			parseContent(str){
				//str = str.replace(/&quot;/g,function(){return '"';}).replace(/&lt;/g,function(){return '<';}).replace(/&gt;/g,function(){return '>';}).replace(/&amp;/g,function(){return '&';}).replace(/&nbsp;/g,function(){return ' ';});
				var c=str.match(new RegExp(/(?<=src=['"])([^'"=\s]+)/g));
				var d=str.match(new RegExp(/(?<=poster=['"])([^'"=\s]+)/g));
				console.log(c,d);
				return {src:c[0],poster:d[0]};
			},
			videoplayerclick(e){
				this.videoPlay(this.current);
			},
			changeCurrent(e){
				this.current = e.detail.current;
				for (let item of this.list) {
					item.flag = false
				}
				this.list[this.current].flag = !this.list[this.current].flag
				if(this.list.length-this.current<3){
					this.pushVideoList();
				}
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
			likeTap(){
				this.list[this.current].islike = !this.list[this.current].islike;
				this.list = [...this.list]				
			},
			avatarTap(){
				uni.showToast({
					icon:'none',
					title:`点击索引为${this.current}的头像`
				})
			},
			messageTap(){
				uni.showToast({
					icon:'none',
					title:`查看索引为${this.current}的评论`
				})
			},
			commentTap(){
				uni.showToast({
					icon:'none',
					title:`分享索引为${this.current}的视频`
				})
			},
			musicTap(e){console.log(e);},
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
				if(typeof this.list[this.current].initialTime !='undefined') this.list[this.current].initialTime = val
			},
			clickVideo(){
				this.list[this.current].flag = !this.list[this.current].flag
			},
			//复制当前链接
			copyUrl(){
				let httpUrl = window.location.href;
				var oInput = document.createElement('input');
				oInput.value = httpUrl;
				document.body.appendChild(oInput);
				oInput.select(); // 选择对象
				document.execCommand("Copy"); // 执行浏览器复制命令
				oInput.className = 'oInput';
				oInput.style.display='none';
				alert("链接复制成功")
			}
		}
	}
</script>

<style>
	.swiper{position: absolute;width: 100%;height: 100%;top: 0;left: 0;z-index: 1;background: #000;}
	.big_play{position:absolute;left:50%;top:50%;z-index: 10;width: 80rpx;height: 80rpx;margin:-40rpx 0 0 -40rpx;}
	.post{position: absolute;bottom: 20rpx;left: 20rpx;width:580rpx;color: #fff;z-index: 10;line-height: 18px;}
	.post .user{font-size: 14px;font-weight: 700;color: #fff;}
	.post .title{font-size: 14px;color: #fff;word-wrap:break-word;word-break: break-all;width: 100%;white-space: normal;} 
</style>
