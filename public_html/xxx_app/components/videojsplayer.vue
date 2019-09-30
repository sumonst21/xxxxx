<template>
	<view :id="id" ref="video"></view>
</template>

<script>
    import videojs from '@/node_modules/video.js/dist/video.js';
    import '@/node_modules/videojs-contrib-hls/dist/videojs-contrib-hls.js';
    //require('@/node_modules/video.js/dist/video.js');
    //require('@/node_modules/videojs-contrib-hls/src/videojs-contrib-hls.js');
	import "@/node_modules/video.js/dist/video-js.css";
	export default {
		data() {
			return {
				player:null,
				id:'video'+parseInt(Math.random()*9999999999)
			};
		},
		props:{
			src:'',
			controls:'',
			poster:''
		},
		mounted() {
			if (!this.player) { 
				this.initialize()
			}
		},
		beforeDestroy() {
			if (this.player) { 
				this.dispose()
			}
		},
		methods: {
			initialize() {	
				// #ifdef H5
				var video = document.createElement('video')  
				video.id = 'video'  
				video.style = 'width: 300px;height: 150px;'  
				video.controls = true  
				var source = document.createElement('source')  
				source.src = this.src;//'http://yf.ugc.v.cztv.com/cztv/ugcvod/2018/04/14/A98CD7B26B06D94A5CEA56AA7D723572/h264_800k_mp4.mp4_playlist.m3u8'  
				video.appendChild(source)  
				this.$refs.video.$el.appendChild(video)  
				videojs('video')  
				this.player = videojs(this.id)
				// #endif  
			},
			dispose(){				
				this.player.dispose()
				this.player = null
			}
			
		}
	}
</script>

<style>

</style>
