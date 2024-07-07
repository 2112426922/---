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
					<view class="basis-sm padding-sm">{{typeText}}人员</view>
					<view class="basis-lg padding-sm">{{info.user || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">{{typeText}}时间</view>
					<view class="basis-lg padding-sm">{{info.createtime || ''}}</view>
				</view>
			</view>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">{{typeText}}内容</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap" v-for="(item, index) in info.content.form_data" :key="index">
					<view class="basis-sm padding-sm">{{item.label}}</view>
					<view class="basis-lg padding-sm">{{item.value}}</view>
				</view>
				<view class="flex flex-wrap" v-if="type == 'inspection'">
					<view class="basis-sm padding-sm">巡检结果</view>
					<view class="basis-lg padding-sm text-red text-bold">{{info.content.work_status || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">备注</view>
					<view class="basis-lg padding-sm">{{info.content.remark || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">现场照片</view>
				</view>
				<view class="grid col-4 grid-square flex-sub padding-top-sm" v-if="info.content.images.length > 0">
					<view class="bg-img" v-for="(image, index) in info.content.images" :key="index" @tap="viewImage"
						:data-url="image">
						<image :src="image" mode="aspectFill"></image>
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
				info: {},
				type: '',
				typeText: ''
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

			let type = e.type;
			_self.type = type;
			if (type == 'inspection') {
				_self.typeText = '巡检';
				uni.setNavigationBarTitle({
					title: '巡检详情'
				});
			}
			if (type == 'maintenance') {
				_self.typeText = '保养';
				uni.setNavigationBarTitle({
					title: '保养详情'
				});
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
			viewImage(e) {
				uni.previewImage({
					urls: _self.info.content.images,
					current: e.currentTarget.dataset.url
				});
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
