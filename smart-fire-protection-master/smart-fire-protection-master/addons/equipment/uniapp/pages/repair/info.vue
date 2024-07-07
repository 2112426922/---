<template>
	<view class="container">
		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">工单信息</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">工单编号</view>
					<view class="basis-lg padding-sm">{{info.repair_code || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修人员</view>
					<view class="basis-lg padding-sm">{{info.repair_username || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">派单日期</view>
					<view class="basis-lg padding-sm">{{info.assigntime || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">工单状态</view>
					<view class="basis-lg padding-sm">{{info.status_text || '-'}}</view>
				</view>
			</view>
		</view>
		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">报修信息</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">报修日期</view>
					<view class="basis-lg padding-sm">{{info.registertime || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">报修人员</view>
					<view class="basis-lg padding-sm">{{info.register_name || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">联系电话</view>
					<view class="basis-lg padding-sm" @click="call(info.register_mobile)">
						{{info.register_mobile || '-'}}
					</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">报修登记</view>
					<view class="basis-lg padding-sm">{{info.register_content != null ? info.register_content : '-'}}
					</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">现场照片</view>
					<view class="basis-lg padding-sm" @click="viewDocument(info.register_image)">
						<image :src="info.register_image" mode="widthFix" style="width: 100%;"
							v-if="info.register_image != ''"></image>
					</view>
				</view>
			</view>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp"
			v-if="info.status == 'repaired' || info.status == 'scrapped'">
			<view class="sub-title">维修结果</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修日期</view>
					<view class="basis-lg padding-sm">{{info.repairtime || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修耗时</view>
					<view class="basis-lg padding-sm">{{info.consuming || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">故障原因</view>
					<view class="basis-lg padding-sm">{{info.failure_cause || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修说明</view>
					<view class="basis-lg padding-sm">{{info.repair_content || '-'}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">现场照片</view>
					<view class="basis-lg padding-sm" @click="viewDocument(info.repair_image)">
						<image :src="info.repair_image" mode="widthFix" style="width: 100%;"
							v-if="info.repair_image != ''"></image>
					</view>
				</view>
			</view>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">设备信息</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备编号</view>
					<view class="basis-lg padding-sm">{{info.equipment_code || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备型号</view>
					<view class="basis-lg padding-sm">{{info.archive_model || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备名称</view>
					<view class="basis-lg padding-sm">{{info.archive_name || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">所在区域</view>
					<view class="basis-lg padding-sm">{{info.archive_region || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备详情</view>
					<view class="basis-lg padding-sm text-blue" @click="goEquipmentInfo(info.equipment_coding)">查看
					</view>
				</view>
			</view>
		</view>
		<view style="height: 80px;"></view>

		<view class="cu-bar bg-white tabbar flex flex-wrap" @click="register()" v-if="info.status == 'registered'">
			<button class="bg-black submit basis-sm" style="border-radius: 0;">维修登记</button>
		</view>
		<view class="cu-bar bg-white tabbar flex flex-wrap" @click="receive()" v-if="info.status == 'pending'">
			<button class="bg-black submit basis-sm" style="border-radius: 0;">我要接单</button>
		</view>
	</view>
</template>

<script>
	var _self
	var repair_id = 0
	var flag = false
	export default {
		data() {
			return {
				info: {}
			};
		},
		onLoad(e) {
			_self = this
			if (!e.id) {
				_self.tui.toast("未知维修工单");
				setTimeout(function() {
					uni.navigateBack();
				}, 1500);

				return;
			}

			repair_id = e.id
			_self.getInfo()
		},
		methods: {
			getInfo() {
				uni.showLoading({
					title: '查询中...'
				});

				_self.$api.repairInfo({
					id: repair_id
				}).then((res) => {
					uni.hideLoading();
					_self.info = res.data;
				}).catch((err) => {
					uni.hideLoading()
					_self.tui.toast(err.msg)
				});
			},
			viewDocument(url) {
				if (url == '') return;

				let suffix = url.slice(-4);
				if (suffix == '.pdf') {
					uni.showLoading({
						title: '加载中...',
						mask: true
					});

					uni.downloadFile({
						url,
						success: function(res) {
							if (res.statusCode === 200) {
								let filePath = res.tempFilePath
								uni.openDocument({
									filePath,
									fileType: 'pdf',
									success: function(res) {},
									fail: function(err) {
										_self.tui.toast('文档加载失败');
									},
									complete: function() {
										uni.hideLoading();
									}
								})
							} else {
								uni.hideLoading();
								_self.tui.toast('文档加载失败');
							}
						},
						fail: function(err) {
							uni.hideLoading();
							_self.tui.toast('文档加载失败');
						}
					});
				} else {
					uni.previewImage({
						urls: [url],
					});
				}
			},
			register() {
				uni.navigateTo({
					url: '/pages/repair/register?repair_id=' + _self.info.repair_id
				})
			},
			receive() {
				if (flag) return
				if (!repair_id) {
					_self.tui.toast('未知维修工单')
					return
				}

				uni.showModal({
					title: '提示',
					content: '是否确认接单？',
					cancelText: '再看看',
					confirmText: '确定',
					cancelColor: '#8799a3',
					confirmColor: '#333333',
					success: res => {
						if (res.confirm) {
							_self.doReceive()
						}
					}
				})
			},
			doReceive() {
				uni.showLoading({
					title: '接单中...'
				})
				_self.$api.receiveRepairs({
					repair_id
				}).then((res) => {
					flag = false
					uni.hideLoading()
					_self.tui.toast('接单成功', '', 'success')
					setTimeout(function() {
						uni.$emit('repairChange', {
							ischange: true,
							type: 'repair'
						})

						_self.getInfo()
					}, 1500)
				}).catch((err) => {
					flag = false
					uni.hideLoading()
					_self.tui.toast(err.msg)
				})
			},
			goEquipmentInfo(coding) {
				uni.navigateTo({
					url: '/pages/equipment/info?coding=' + coding
				})
			}
		}
	}
</script>

<style lang="scss">
	.sub-title {
		color: #333333;
		font-size: 28rpx;
		padding: 10rpx 20rpx;
		font-weight: bold;
	}

	.info {
		margin-top: 10rpx;
		border: 1px #F1F1F1 solid;
		border-bottom: 0;
		font-size: 26rpx;
		color: #606266;

		.flex {
			border-bottom: 1px #F1F1F1 solid;
			background-color: #FFFFFF;

			view {
				padding: 8px 10px;
				box-sizing: border-box;
			}

			.basis-sm {
				border-right: 1px #F1F1F1 solid;
			}
		}
	}

	.cu-bar.tabbar {
		position: fixed;
		width: 100%;
		left: 0;
		bottom: 0;
	}

	.cu-list+.cu-list {
		margin-top: 20rpx;
	}

	.card {
		margin: 20rpx;
		border-radius: 10rpx;
		padding: 20rpx 0;
	}
</style>
