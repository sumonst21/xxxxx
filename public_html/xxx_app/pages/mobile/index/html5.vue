<template>
	<view class="body">
		<cover-view class="header">
			<view class="left"><fa type="search" size="26" color="#fff"></fa></view>
			<view class="center">
				<text class="on">推荐</text> 
				<text>深圳</text>
			</view>
			<view class="right"></view>
		</cover-view>
		
        <!--vant van-swipe 滑动组件 -->
        <swiper :show-indicators="false" @change="onChange" :current="current" vertical :loop="false" class="swiper">
            <swiper-item v-for="(item, index) in list" :key="index">
                <div class="video">
					<vue-video-player class="video-player-box"
						 ref="videoPlayer"
                         :poster="item.user.avatar"
						 :src="item.url"
						 preload="auto"
						 @tap="playvideo">
				    </vue-video-player>
                    <!-- 封面 --
                    <image v-show="isVideoShow" class="play" @click="playvideo" :src="item.user.avatar"/>
                    <!-- 播放暂停按钮 --
                    <image v-show="iconPlayShow" class="icon_play" @click="playvideo" src="/video/icon_play.png"/>
					-->
					<cover-view class="progress_view"><progress percent="40" active stroke-width="3" /></cover-view>
                </div>
                <!-- 底部作品描述 -->
				<cover-view class="post">
					<view class="user">@{{item.user.nickname}}</view>
					<view class="title">{{item.title}}</view>
				</cover-view>
                <!-- 右侧点赞、分享功能 -->
				<cover-view class="operation">
					<view class="avatar">
						<image :src="item.user.avatar" mode="aspectFill"></image>
						<fa type="plus" size="20" color="#fff" ></fa>
					</view>
					<view class="item like">
						<fa type="heart-o" size="30" color="#fff" ></fa>
						<text>41.3W</text>
					</view>
					<view class="item comment">
						<fa type="commenting-o" size="30" color="#fff" ></fa>
						<text>355</text>
					</view>
					<view class="item message">
						<fa type="send" size="30" color="#fff" ></fa>
						<text>1670</text>
					</view>
					<view class="item music">
						<view class="cd">
							<image :src="item.user.avatar" mode="aspectFill"></image>
						</view>
					</view>
				</cover-view>
            </swiper-item>
        </swiper>
	</view>
</template>

<script>
	import vueVideoPlayer from '@/node_modules/vue-video-player/src/player'
	//import vueVideoPlayer from '@/node_modules/vue-video-player/dist/vue-video-player'
	//import '@/node_modules/videojs-contrib-hls/dist/videojs-contrib-hls.min.js'
	
	import '@/node_modules/video.js/dist/video-js.css'
	import '@/node_modules/vue-video-player/src/custom-theme.css'

	export default {
		components:{
			vueVideoPlayer,
		},
		computed: {
		  player() {
			return this.$refs.videoPlayer[this.current].player
		  }
		},
		data() {
            let u = navigator.userAgent;
			return {
				list:[
					/*http://eey.com-baidu-com.com/2017/88443/hls/index.m3u8*/
					{id:1,user:{nickname:'cansnow',avatar:'https://img-cdn-qiniu.dcloud.net.cn/uniapp/images/shuijiao.jpg'},title:"佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼",url:'http://eey.com-baidu-com.com/2017/88443/hls/index.m3u8'},
					{id:2,user:{nickname:'cansnow',avatar:'https://img-cdn-qiniu.dcloud.net.cn/uniapp/images/shuijiao.jpg'},title:"佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼",url:'https://dcloud-img.oss-cn-hangzhou.aliyuncs.com/guide/uniapp/%E7%AC%AC1%E8%AE%B2%EF%BC%88uni-app%E4%BA%A7%E5%93%81%E4%BB%8B%E7%BB%8D%EF%BC%89-%20DCloud%E5%AE%98%E6%96%B9%E8%A7%86%E9%A2%91%E6%95%99%E7%A8%8B@20181126.mp4'},
					{id:3,user:{nickname:'cansnow',avatar:'https://img-cdn-qiniu.dcloud.net.cn/uniapp/images/shuijiao.jpg'},title:"佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼",url:'http://video.jishiyoo.com/f843c93d4f3f4bee844690fe1fdfc750/28e2548c47ce4d0cb310697f0ca4c7a3-14818dd00765a9a86871da6734b1be28-ld.mp4'},
					{id:4,user:{nickname:'cansnow',avatar:'https://img-cdn-qiniu.dcloud.net.cn/uniapp/images/shuijiao.jpg'},title:"佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼偶偶偶偶所都偶佛收到货哦哦哦及发生的客服号我哦吼",url:'http://video.jishiyoo.com/c082827a58584a2c864fa403f1aafabf/d5333562c5c74bcdb5bd3fe92073cd9c-6375039a2dd07d44dfaf66c1a32287ff-ld.mp4'}
				],
				current: 0,
                isVideoShow: true,
                playOrPause: true,
                video: null,
                iconPlayShow: true,
                isAndroid: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, // android终端
                isiOS: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), // ios终端
                tabIndex:0,
                showShareBox:false,
			}
		},
		mounted() {
			this.player.play();
		},
		methods: {
			videoErrorCallback(){},
			//改变收藏状态
			changeFollow(item,index){
				this.videoList[index].follow = !this.videoList[index].follow
			},
			//展示分享弹窗
			changeShare(){
				this.showShareBox=true
			},
			//取消分享
			cancelShare(){
				this.showShareBox=false
			},
			//滑动改变播放的视频
			onChange(e) {
				//改变的时候 暂停当前播放的视频
				this.player.pause();
				this.playOrPause = false;
				this.current = e.detail.current;
				if(e.detail.current+2>=this.list.length){
					console.log(e.detail.current,this.list.length);
				}
				if (this.isiOS) {
					//ios切换直接自动播放下一个
					this.isVideoShow = false;
					this.pauseVideo()
				}else{
					//安卓播放时重置显示封面。图标等
					this.isVideoShow = true;
					this.iconPlayShow = true
				}
			},
			playvideo(event) {
				let video = this.$refs.videoPlayer[this.current].player;
				console.log("playvideo：" + this.current);
				this.isVideoShow = false;
				this.iconPlayShow = false
				this.player.play();
				window.onresize = function () {
					video.style.width = window.innerWidth + "px";
					video.style.height = window.innerHeight + "px";
				}

			},
			pauseVideo() { //暂停\播放
				let video = this.$refs.videoPlayer[this.current].player;
				console.log("pauseVideo" + this.current)
				if (this.playOrPause) {
					video.pause()
					this.iconPlayShow = true
				} else {
					video.play()
					this.iconPlayShow = false
				}
				this.playOrPause = !this.playOrPause
			},
			onPlayerEnded(player) { //视频结束
				this.isVideoShow = true
				this.current += this.current
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
	/*@import "../../node_modules/video.js/dist/video-js.css";*/
	.swiper{position: absolute;width: 100%;height: 100%;top: 0;left: 0;z-index: 1;background: #000;}
	.video{width: 100%;height: 100%;display: flex;align-items: center;}
	.video .vjs-tech{width: 100%;min-height: 225px;height:auto;max-height: 100%;}
	.progress_view{position:absolute;bottom:0;left:0;right: 0;}
	.operation{position: absolute;right: 10rpx;bottom: 40rpx;z-index: 10;display:flex;flex-direction: column;}
	.operation .item {display: flex;flex-direction: column;width: 120rpx;text-align: center;margin-top: 40rpx;}
	.operation .avatar{width: 120rpx;height: 80rpx;display: block;position:relative;text-align: center;}
	.operation .avatar image{width: 80rpx;height: 80rpx;border-radius: 100%;overflow: hidden;}
	.avatar .fa-icon{position: absolute;bottom: -20rpx;left: 50%;margin-left: -10px;background: #f00;border-radius: 100%;width: 20px;height: 20px;text-align: center;}
	.operation text{color:#fff;font-size: 14px;}
	.cd{border-radius: 50%;width: 80rpx;height: 80rpx;background: url(../../../static/cd.jpg);margin: 0 auto;}
	.cd image{border-radius: 100%;width: 60rpx;height: 60rpx;margin: 10rpx;}
	
	.post{position: absolute;bottom: 20rpx;left: 20rpx;width:580rpx;color: #fff;z-index: 10;line-height: 18px;}
	.post .user{font-size: 14px;font-weight: 700;color: #fff;}
	.post .title{font-size: 14px;color: #fff;word-wrap:break-word;word-break: break-all;width: 100%;white-space: normal;} 
</style>
