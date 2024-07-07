<template>
	<view class="login">
		<view class="content">
			<!-- 头部logo -->
			<view class="header">
				<image :src="logoImage"></image>
			</view>
			<!-- 主体表单 -->
			<view class="main">
				<wInput v-model="mobile" type="text" maxlength="11" placeholder="手机号码" :focus="isFocus"></wInput>
				<wInput v-model="password" type="password" placeholder="登录密码"></wInput>
			</view>
			<wButton class="wbutton" text="登 录" :rotate="isRotate" @click="startLogin"></wButton>

			<!-- 其他登录 -->
			<!-- <view class="other_login cuIcon">
				<view class="login_icon">
					<view class="cuIcon-weixin" @tap="login_weixin"></view>
				</view>
			</view> -->

			<!-- 底部信息 -->
			<view class="footer">
				<view @click="forget()">找回密码</view>
			</view>

			<view style="height: 100rpx;"></view>
		</view>
	</view>
</template>

<script>
	import wInput from '../../components/watch-login/watch-input.vue' //input
	import wButton from '../../components/watch-login/watch-button.vue' //button

	var _self
	var code
	export default {
		data() {
			return {
				logoImage: 'data:image/png;base64,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',
				mobile: '', //用户/电话
				password: '', //密码
				isRotate: false, //是否加载旋转
				isFocus: true // 是否聚焦
			};
		},
		components: {
			wInput,
			wButton,
		},
		mounted() {

		},
		onLoad() {
			_self = this
			_self.isLogin()

			wx.login({
				success(res) {
					if (res.code) {
						code = res.code
					}
				}
			})
		},
		methods: {
			isLogin() {
				//判断缓存中是否登录过，直接登录
				try {
					const token = uni.getStorageSync('equipment_token');
					const equipmentManage = uni.getStorageSync('equipment_manage');
					if (token) {
						//有登录信息
						getApp().globalData.token = token
						getApp().globalData.equipmentManage = equipmentManage
						const eventChannel = _self.getOpenerEventChannel()
						eventChannel.emit('isLoginFromLogin', {
							isLogin: true
						})

						uni.$emit('loginStatusEvent', {
							isLogin: true
						})
					}
				} catch (e) {
					// error
				}
			},
			startLogin(e) {
				//登录
				if (_self.isRotate) {
					//判断是否加载中，避免重复点击请求
					return false
				}
				if (_self.mobile.length == "") {
					_self.tui.toast('手机号码不能为空')
					return
				}
				if (_self.password.length < 5) {
					_self.tui.toast('登录密码不正确')
					return
				}

				_self.isRotate = true
				setTimeout(function() {
					_self.isRotate = false
				}, 3000)
				uni.showLoading({
					title: '登录中...'
				})
				_self.$api.login({
					code,
					mobile: _self.mobile,
					password: _self.password
				}).then((res) => {
					uni.hideLoading()
					_self.isRotate = false
					_self.tui.toast('登录成功', '', 'success')

					getApp().globalData.token = res.data.token
					getApp().globalData.equipmentManage = res.data.equipment_manage
					uni.setStorageSync('equipment_token', res.data.token)
					uni.setStorageSync('equipment_openid', res.data.openid)
					uni.setStorageSync('equipment_manage', res.data.equipment_manage)
					setTimeout(function() {
						const eventChannel = _self.getOpenerEventChannel()
						eventChannel.emit('isLoginFromLogin', {
							isLogin: true
						})

						uni.$emit('loginStatusEvent', {
							isLogin: true
						})
						uni.navigateBack()
					}, 1500)
				}).catch((err) => {
					uni.hideLoading()
					_self.password = ''
					_self.isRotate = false
					_self.tui.toast(err.msg)
				})
			},
			login_weixin() {
				//微信登录
				uni.showToast({
					icon: 'none',
					position: 'bottom',
					title: '...'
				});
			},
			forget() {
				uni.showModal({
					title: '提示',
					content: '请联系系统管理员',
					showCancel: false,
					confirmText: '知道了',
					confirmColor: '#333333'
				});
			}
		}
	}
</script>

<style>
	@import url("../../components/watch-login/css/icon.css");
	@import url("./css/main.css");

	page {
		background-color: #FFFFFF;
	}
</style>
