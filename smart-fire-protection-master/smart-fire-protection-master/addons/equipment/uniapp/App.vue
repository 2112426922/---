<script>
	export default {
		globalData: {
			token: '',
			equipmentManage: 0
		},
		onLaunch: function() {
			console.log('App Launch')
			
			// 获取缓存信息Token
			this.getStorageInfo()
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		},
		methods: {
			/**
			 * 获取缓存信息
			 */
			getStorageInfo() {
				this.globalData.token = uni.getStorageSync("equipment_token");
				this.globalData.equipmentManage = uni.getStorageSync("equipment_manage");
			}
		}
	}
</script>

<style>
	/*每个页面公共css */
	@import "components/colorui/main.css";
	@import "components/colorui/icon.css";
</style>
