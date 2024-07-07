<template>
	<view>
		<scan v-if="pageCur=='scan'"></scan>
		<workbench v-if="pageCur=='workbench'"></workbench>
	</view>
</template>

<script>
	import scan from '@/pages/index/scan.vue'
	import workbench from '@/pages/index/workbench.vue'

	var _self
	var isLogin
	var equipmentManage
	export default {
		data() {
			return {
				login: false,
				pageCur: 'scan'
			}
		},
		components: {
			scan,
			workbench
		},
		async onShow() {
			if (isLogin) {
				_self.refreshComponent()
			}
		},
		onLoad() {
			_self = this
			isLogin = !getApp().globalData.token ? false : true
			equipmentManage = getApp().globalData.equipmentManage

			_self.login = isLogin
			if (isLogin) {
				if (equipmentManage) {
					_self.pageCur = 'workbench'
				} else {
					_self.pageCur = 'scan'
				}
			} else {
				wx.login({
					success(res) {
						if (res.code) {
							_self.goWeLogin(res.code)
						}
					}
				})
			}

			uni.$on('loginStatusEvent', function(data) {
				_self.login = data.isLogin
				isLogin = data.isLogin
				equipmentManage = getApp().globalData.equipmentManage
				if (data.isLogin && equipmentManage) {
					_self.pageCur = 'workbench'
				} else {
					_self.pageCur = 'scan'
				}
			})
		},
		onUnload() {
			uni.$off('loginStatusEvent')
		},
		methods: {
			refreshComponent() {
				_self.pageCur = 'scan'
				if (isLogin && equipmentManage) {
					_self.$nextTick(() => {
						_self.pageCur = 'workbench'
					})
				}
			},
			goWeLogin(code) {
				_self.$api.welogin({
					code
				}).then((res) => {
					getApp().globalData.token = res.data.token
					getApp().globalData.equipmentManage = res.data.equipment_manage
					uni.setStorageSync('equipment_token', res.data.token)
					uni.setStorageSync('equipment_openid', res.data.openid)
					uni.setStorageSync('equipment_manage', res.data.equipment_manage)

					if (res.data.equipment_manage) {
						_self.pageCur = 'workbench'
					} else {
						_self.pageCur = 'scan'
					}
				}).catch((err) => {
					_self.pageCur = 'scan'
				})
			}
		}
	}
</script>

<style>

</style>
