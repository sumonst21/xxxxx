<template>
	<view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				src:"",
			}
		},
		onReady(){		
			var self = this;
			uni.chooseVideo({
				count: 1,
				sourceType: ['camera', 'album'],
				success: function (res) {
					self.src = res.tempFilePath;
				}
			});
		},
		methods: {
			upload(){
				var self = this;
				uni.uploadFile({
					url: 'https://www.example.com/upload', //仅为示例，非真实的接口地址
					filePath: self.src,
					name: 'file',
					formData: {
						'user': 'test'
					},
					success: (uploadFileRes) => {
						console.log(uploadFileRes.data);
					}
				});
			}
		}
	}
</script>

<style>

</style>
