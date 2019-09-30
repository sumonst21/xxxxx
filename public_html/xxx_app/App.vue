<script>
	export default {
		onLaunch: function () {
			console.log('App Launch')
		},
		onShow: function () {
			console.log('App Show')
		},
		onHide: function () {
			console.log('App Hide')
		}
	}
</script>

<style>
	/*每个页面公共css */
	page{background: #161824;}
	.header{display: flex;flex-direction: row;position: fixed;top: 0;width: 100%;z-index: 10;padding:10px;}
	.header .left,.header .center,.header .right{flex:1;color: #fff;}
	.header .center{display: flex;flex-direction: row;}
	.header .center text{flex: 1;}
	.header .center text.on{font-weight: 700;}
	text,view{color:#fff;}
</style>
