<template>
	<cover-view class="operation">
		<view class="avatar" @tap="avatarTap">
			<image :src="avatar" mode="aspectFill"></image>
			<fa type="plus" size="20" color="#fff" ></fa>
		</view>
		<view class="item like" @tap="likeTap">
			<fa type="heart" size="30" color="#f00" v-if="islike"></fa>
			<fa type="heart-o" size="30" color="#fff" v-else="islike"></fa>
			<text>41.3W</text>
		</view>
		<view class="item comment" @tap="commentTap">
			<fa type="commenting-o" size="30" color="#fff" ></fa>
			<text>355</text>
		</view>
		<view class="item message" @tap="messageTap">
			<fa type="send" size="30" color="#fff" ></fa>
			<text>1670</text>
		</view>
		<view class="item music" @tap="musicTap">
			<view class="cd">
				<image :src="avatar" mode="aspectFill"></image>
			</view>
		</view>
	</cover-view>
</template>

<script>
	export default {
		data() {
			return {
				
			};
		},
		props:{
			avatar:'',
			islike:'',
		},
		methods: {
			avatarTap(e){this.$emit('avatarTap');},
			likeTap(e){this.$emit('likeTap');},
			commentTap(e){this.$emit('commentTap');},
			messageTap(e){this.$emit('messageTap');},
			musicTap(e){this.$emit('musicTap');},
		},
	}
</script>

<style>
	.operation{position: absolute;right: 10rpx;bottom: 40rpx;z-index: 10;display:flex;flex-direction: column;}
	.operation .item {display: flex;flex-direction: column;width: 120rpx;text-align: center;margin-top: 40rpx;}
	.operation .avatar{width: 120rpx;height: 80rpx;display: block;position:relative;text-align: center;}
	.operation .avatar image{width: 80rpx;height: 80rpx;border-radius: 100%;overflow: hidden;}
	.avatar .fa-icon{position: absolute;bottom: -20rpx;left: 50%;margin-left: -10px;background: #f00;border-radius: 100%;width: 20px;height: 20px;text-align: center;}
	.operation text{color:#fff;font-size: 14px;}
	.cd{border-radius: 50%;width: 80rpx;height: 80rpx;background: url(../static/cd.jpg);margin: 0 auto;}
	.cd image{border-radius: 100%;width: 60rpx;height: 60rpx;margin: 10rpx;}
</style>
