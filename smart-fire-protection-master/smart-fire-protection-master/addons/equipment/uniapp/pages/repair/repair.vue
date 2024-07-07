<template>
	<view class="container">
		<form>
			<view class="cu-form-group align-start margin-top-sm">
				<view class="title">报修登记</view>
				<textarea maxlength="-1" @input="textareaInput" placeholder="报修问题描述"></textarea>
			</view>
			<view class="cu-bar bg-white margin-top-sm">
				<view class="action">
					现场照片
				</view>
			</view>
			<view class="cu-form-group">
				<view class="grid col-4 grid-square flex-sub">
					<view class="bg-img" @tap="viewImage" :data-url="imgList.fullurl" v-if="imgUpload">
						<image :src="imgList.fullurl" mode="aspectFill"></image>
						<view class="cu-tag bg-red" @tap.stop="delImg">
							<text class='cuIcon-close'></text>
						</view>
					</view>
					<view class="solids" @tap="chooseImage" v-if="!imgUpload">
						<text class='cuIcon-cameraadd'></text>
					</view>
				</view>
			</view>
		</form>

		<view class="cu-bar bg-white tabbar flex flex-wrap" @click="submit()">
			<button class="bg-black submit basis-sm" style="border-radius: 0;">确认提交</button>
		</view>
	</view>
</template>

<script>
	import Config from '@/utils/config.js'

	var _self
	var equipment_id = 0
	var flag = false
	export default {
		data() {
			return {
				content: '',
				imgList: {
					fullurl: '',
					url: ''
				},
				imgUpload: false
			};
		},
		onLoad(e) {
			_self = this
			if (!e.equipment_id) {
				_self.tui.toast("未知设备")
				setTimeout(function() {
					uni.navigateBack()
				}, 1500)
				return
			}

			equipment_id = e.equipment_id
		},
		methods: {
			chooseImage() {
				uni.chooseImage({
					count: 1,
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (chooseImageRes) => {
						const tempFilePaths = chooseImageRes.tempFilePaths
						uni.uploadFile({
							url: Config.host + 'api/common/upload',
							filePath: tempFilePaths[0],
							name: 'file',
							header: {
								'token': uni.getStorageSync('equipment_token')
							},
							success: (uploadFileRes) => {
								let result = JSON.parse(uploadFileRes.data)
								_self.imgUpload = true
								_self.imgList = result.data
							},
							fail: () => {
								_self.imgUpload = false
								_self.tui.toast('图片上传失败')
							}
						})
					}
				})
			},
			viewImage(e) {
				uni.previewImage({
					urls: [_self.imgList.fullurl],
					current: e.currentTarget.dataset.url
				});
			},
			delImg() {
				uni.showModal({
					title: '提示',
					content: '确定要删除这张照片吗？',
					cancelText: '再看看',
					confirmText: '确定',
					cancelColor: '#8799a3',
					confirmColor: '#333333',
					success: res => {
						if (res.confirm) {
							_self.imgList = {}
							_self.imgUpload = false
						}
					}
				})
			},
			textareaInput(e) {
				_self.content = e.detail.value
			},
			submit() {
				if (flag) return
				if (!equipment_id) {
					_self.tui.toast('未知设备')
					return
				}
				if (_self.content == "") {
					_self.tui.toast('报修登记不能为空')
					return
				}

				uni.showLoading({
					title: '提交中...'
				})
				_self.$api.repair({
					equipment_id,
					content: _self.content,
					register_image: _self.imgList.url
				}).then((res) => {
					flag = false
					uni.hideLoading()
					_self.tui.toast('报修成功', '', 'success')
					setTimeout(function() {
						const eventChannel = _self.getOpenerEventChannel()
						eventChannel.emit('doRefresh', {
							ischange: true,
							type: 'register'
						})

						uni.navigateBack()
					}, 1500)
				}).catch((err) => {
					flag = false
					uni.hideLoading()
					_self.tui.toast(err.msg)
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #f1f1f1;
	}

	.cu-bar.tabbar {
		position: fixed;
		width: 100%;
		left: 0;
		bottom: 0;
	}
</style>
