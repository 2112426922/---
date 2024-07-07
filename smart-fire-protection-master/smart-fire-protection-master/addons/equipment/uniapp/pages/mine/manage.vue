<template>
	<view class="container">
		<view class="lists" v-if="login">
			<view class="cu-card case">
				<view class="cu-item">
					<view class="cu-list menu-avatar">
						<view class="cu-item">
							<view class="cu-avatar round" :style="[{'background-image': 'url(' + avatar + ')'}]">
							</view>
							<view class="content">
								<view>{{user.nickname || ''}}</view>
								<view>电话：{{user.mobile || ''}}</view>
								<view>部门：{{user.department || ''}}</view>
							</view>
						</view>
					</view>
				</view>
			</view>

			<view class="cu-list menu sm-border card-menu">
				<view class="cu-item arrow_" @click="callManage()">
					<view class="content red-text">
						<text class="cuIcon-phone text-black_"></text>
						<text class="text-black_">一键报警</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="logout()">
					<view class="content">
						<text class="cuIcon-exit text-black"></text>
						<text class="text-black">退出登录</text>
					</view>
				</view>
			</view>
		</view>
		<view v-else>
			<view class="empty nologin-content">
				<image src="../../static/nologin.png" mode="widthFix"></image>
				<view class="text-gray">请先登录账号</view>
				<button class="cu-btn block bg-black lg" @click="goLogin()">
					<text class="cuIcon-lock margin-right-xs"></text> 登录账号</button>
			</view>
		</view>
	</view>
</template>

<script>
	var _self
	var isLogin
	export default {
		data() {
			return {
				login: false,
				user: {},
				avatar: '',
				managePhone: ''
			};
		},
		onShow() {
			if (!_self.login) {
				isLogin = !getApp().globalData.token ? false : true
				if (isLogin) {
					_self.login = isLogin
					_self.getStaffInfo()
				}
			}
		},
		onLoad(e) {
			_self = this

			isLogin = !getApp().globalData.token ? false : true
			_self.login = isLogin
			if (!isLogin) {
				wx.login({
					success(res) {
						if (res.code) {
							_self.goWeLogin(res.code)
						}
					}
				})
			} else {
				_self.getStaffInfo()
			}

			_self.getSystemInfo()
		},
		methods: {
			getSystemInfo() {
				_self.$api.getSystemInfo().then((res) => {
					_self.managePhone = res.data.manage_phone
				}).catch((err) => {

				})
			},
			getStaffInfo() {
				uni.showLoading({
					title: '加载中...'
				})

				_self.$api.getStaffInfo().then((res) => {
					uni.hideLoading()
					_self.user = res.data
					_self.avatar = res.data.avatar
				}).catch((err) => {
					uni.hideLoading()
					_self.tui.toast(err.msg)
				})
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

					_self.login = true
					_self.getStaffInfo()
				}).catch((err) => {

				})
			},
			goLogin() {
				uni.navigateTo({
					url: '/pages/login/login',
					events: {
						// 为指定事件添加一个监听器，获取被打开页面传送到当前页面的数据
						isLoginFromLogin: function(data) {
							if (data && data.isLogin) {
								_self.login = true
								_self.getStaffInfo()
							}
						}
					}
				})
			},
			logout() {
				uni.showModal({
					title: '提示',
					content: '是否确认要操作退出登录？',
					success: function(res) {
						if (res.confirm) {
							_self.doLogout()
						}
					}
				});
			},
			doLogout() {
				uni.showLoading({
					title: '退出中...'
				})

				_self.$api.logout().then((res) => {
					uni.hideLoading()
					_self.clearStorage()
				}).catch((err) => {
					uni.hideLoading()
					_self.clearStorage()
					// _self.tui.toast(err.msg)
				})
			},
			clearStorage() {
				try {
					_self.login = false
					getApp().globalData.token = ''
					getApp().globalData.equipmentManage = 0
					uni.setStorageSync('equipment_token', '')
					uni.setStorageSync('equipment_openid', '')
					uni.setStorageSync('equipment_manage', '')

					uni.$emit('loginStatusEvent', {
						isLogin: false
					})

					_self.tui.toast('退出登录成功')
				} catch (e) {
					_self.tui.toast('退出失败，请稍后再试')
				}
			},
			callManage() {
				let managePhone = _self.managePhone
				if (managePhone == '') {
					_self.tui.toast('暂无管理员联系方式')
					return;
				}

				uni.makePhoneCall({
					phoneNumber: managePhone
				})
			}
		}
	}
</script>

<style lang="scss">
	.cu-card>.cu-item {
		margin: 20rpx;
	}
	.red-text {
	    color: red !important;
		font-weight: bold;
	}


	.cu-list.menu-avatar>.cu-item {
		height: 250rpx;

		.cu-avatar {
			width: 150rpx;
			height: 150rpx;
			left: 40rpx;
		}

		.content {
			left: 240rpx;
			line-height: 2;

			>view:first-child {
				font-size: 40rpx;
				font-weight: bold;
			}
		}
	}

	.cu-list.menu-avatar>.cu-item:after {
		border: 0;
	}

	.cu-list.card-menu {
		margin: 0 20rpx 20rpx;
		border-radius: 10rpx;
	}

	.empty {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;

		margin-top: 20vh;
		text-align: center;

		image {
			width: 40%;
		}

		.cu-btn {
			margin-top: 220rpx;
		}
	}

	.nologin-content {
		height: 100vh;
		margin-top: 0;
		background-color: #FFFFFF;
	}
</style>
