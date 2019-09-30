<template>
	<view>
		<view class="bg"></view>
		<view class="userinfo">
			<view class="user">
				<image class="avatar" src="../../../static/logo.png"></image>
				<button class="edit">编辑资料</button>
				<button class="add"><fa type="plus" size="14" color="#fff"></fa></button>
			</view>
			<view class="nickname mt20">Cansnow</view>
			<view class="aboutme mt20">sadsghdhgftrhfyjknfgjfmjgfjgh</view>
			<view class="tags mt20">
				<text class="tag">深圳</text>
				<text class="tag">深圳</text>
				<text class="tag">深圳</text>
				<text class="tag">深圳</text>
				<text class="tag">深圳</text>
				<text class="tag">深圳</text>
				<text class="tag">+性别年龄学校等标签</text>
			</view>
			<view class="total mt20">
				<view class="zan"><text>0</text>获赞</view>
				<view class="follow"><text>0</text>关注</view>
				<view class="fans"><text>0</text>粉丝</view>
			</view>
			<view class="detail mt20">
				<view class="tabs">
					<view class="header">
						<view class="tab" @tap="switchTab" data-target="zuopin">作品0</view>
						<view class="tab" @tap="switchTab" data-target="dongtai">动态0</view>
						<view class="tab" @tap="switchTab" data-target="xihuan">喜欢164</view>
						<view class="cursor"></view>
					</view>
					<scroll-view class="panels" scroll-x="true" @scroll="scroll" scroll-left="0">
						<view class="panel" id="zuopin"></view>
						<view class="panel" id="dongtai"></view>
						<view class="panel" id="xihuan"></view>
					</scroll-view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				login: false,
				avatarUrl: '/static/logo.png',
				uerInfo: {}
			}
		},
		methods: {
			switchTab(e){
				
			},
			goLogin() {
				if (!this.login) {
					console.log('点击前往登录');
				}
			}
		}
	}
</script>

<style>
	.userinfo{padding: 0 20rpx;}
	.bg{background-image:url(../../../static/userbg.jpg);height:300rpx;}
	.user{position: relative;text-align:right;height: 150rpx;}
	.user .avatar{width: 180rpx;height: 180rpx;border-radius:50%;position: absolute;top:-40rpx;left: 0;border: 5px solid #161824;}
	.user button{display:inline-block;background: #343A40;color:#fff;height: 80rpx;line-height: 80rpx;margin: 20rpx;margin-right: 0;border-radius: 8px;font-size:14px;}
	.user button.edit{width:200rpx;}
	.user button.add{width: 64rpx;}
	.mt20{margin-top: 20rpx;}
	.nickname{font-size: 16px;}
	.aboutme{font-size: 14px;}
	.tags{display: flex;flex-direction: row;flex-wrap: wrap;}
	.tags .tag{background: #242730;border-radius: 2px;color: #929398;font-size: 12px;padding:5px ;margin: 0 10rpx 10rpx 0;}
	.total{display: flex;flex-direction: row;font-size: 14px;color:#86878c;}
	.total view{color:#86878c;margin:0 40rpx 0 0;}
	.total text{color:#fff;font-weight: 700;padding:0 10rpx;}
	.tabs{}
	.tabs .header{display: flex;flex-direction: row;position: relative;}
	.tabs .tab{text-align: center;flex: 1;}
	.tabs .cursor{position: absolute;height: 2px;background: #28A745;width: 33%;bottom: 0;}
	.tabs .panels{}
	.tabs .panel{}
	
</style>
