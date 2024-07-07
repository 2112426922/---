<template>
	<view class="container">
		<form>
			<view class="cu-form-group margin-top-sm">
				<view class="title">维修状态</view>
				<picker @change="pickerChange" :value="statusIndex" :range="statusPicker">
					<view class="picker">
						{{statusPicker[statusIndex]}}
					</view>
				</picker>
			</view>
			<view class="cu-form-group margin-top-sm">
				<view class="title">故障原因</view>
				<picker @change="causeChange" :value="causeIndex" :range="causePicker">
					<view class="picker">
						{{causePicker[causeIndex]}}
					</view>
				</picker>
			</view>
			<view class="cu-form-group align-start margin-top-sm">
				<view class="title">维修说明</view>
				<textarea maxlength="-1" @input="textareaInput" placeholder="维修情况说明"></textarea>
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
	var repair_id = 0
	var flag = false
	export default {
		data() {
			return {
				content: '',
				imgList: {
					fullurl: '',
					url: ''
				},
				imgUpload: false,
				statusIndex: 0,
				statusPicker: ['已维修', '停机报废'],
				statusArr: ['repaired', 'scrapped'],
				causePicker: [],
				causeIdArr: [],
				causeIndex: 0
			};
		},
		onLoad(e) {
			_self = this
			if (!e.repair_id) {
				_self.tui.toast("未知维修工单")
				setTimeout(function() {
					uni.navigateBack()
				}, 1500)
				return
			}

			repair_id = e.repair_id
			_self.failureCause()
		},
		methods: {
			failureCause() {
				_self.$api.failureCause().then((res) => {
					var lists = res.data
					var causeIdArr = [];
					var causePicker = [];
					
					lists.forEach(function(item, index) {
						causeIdArr.push(item.id)
						causePicker.push(item.name)
					})
					_self.causeIdArr = causeIdArr
					_self.causePicker = causePicker
				})
			},
			pickerChange(e) {
				this.statusIndex = e.detail.value
			},
			causeChange(e) {
				this.causeIndex = e.detail.value
			},
			chooseImage() {
				uni.chooseImage({
					count: 1,
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
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
				if (!repair_id) {
					_self.tui.toast('未知报修记录')
					return
				}
				if (_self.content == "") {
					_self.tui.toast('维修说明不能为空')
					return
				}

				uni.showLoading({
					title: '提交中...'
				})
				_self.$api.register({
					repair_id,
					failure_cause_id: _self.causeIdArr[_self.causeIndex],
					repair_content: _self.content,
					repair_image: _self.imgList.url,
					repair_status: _self.statusArr[_self.statusIndex]
				}).then((res) => {
					flag = false
					uni.hideLoading()
					_self.tui.toast('登记成功', '', 'success')
					setTimeout(function() {
						uni.$emit('repairChange', {
							ischange: true,
							type: 'repair'
						})

						uni.navigateBack({
							delta: 2
						})
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
