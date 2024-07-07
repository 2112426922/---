<template>
	<view class="container">
		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">基础信息</view>
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
					<view class="basis-sm padding-sm">维修人员</view>
					<view class="basis-lg padding-sm">{{info.repair_user || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修时间</view>
					<view class="basis-lg padding-sm">{{info.repairtime || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修时长</view>
					<view class="basis-lg padding-sm">{{info.consuming || ''}}</view>
				</view>
			</view>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title marin-top-sm">维修结果</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修结果</view>
					<view class="basis-lg padding-sm text-red text-bold">{{info.status_text || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">故障原因</view>
					<view class="basis-lg padding-sm">{{info.failure_cause || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">维修内容</view>
					<view class="basis-lg padding-sm">{{info.repair_content || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">现场照片</view>
					<view class="basis-lg padding-sm" @click="viewDocument(info.repair_image)">
						<image :src="info.repair_image" mode="widthFix" style="width: 100%;"
							v-if="info.repair_image != ''">
						</image>
					</view>
				</view>
			</view>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title marin-top-sm">报修登记</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">报修人员</view>
					<view class="basis-lg padding-sm">{{info.register_user || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">报修时间</view>
					<view class="basis-lg padding-sm">{{info.registertime || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">报修内容</view>
					<view class="basis-lg padding-sm">{{info.register_content || ''}}</view>
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

		<view style="height: 20rpx;"></view>
	</view>
</template>

<script>
	var _self;
	export default {
		data() {
			return {
				info: {}
			};
		},
		onLoad(e) {
			_self = this;
			if (!e.id) {
				_self.tui.toast("未知记录");
				setTimeout(function() {
					uni.navigateBack();
				}, 1500);

				return;
			}

			_self.getInfo(e.id);
		},
		methods: {
			getInfo(id) {
				uni.showLoading({
					title: '查询中...'
				});

				_self.$api.getRecordInfo({
					id
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

		.grid {
			border-bottom: 1px #F1F1F1 solid;
			background-color: #FFFFFF;
		}
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
