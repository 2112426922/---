<template>
	<view class="container">
		<z-paging ref="paging" v-model="repairs" empty-view-text="暂无维修工单" @query="getRepairs">
			<view slot="top">
				<scroll-view scroll-x class="bg-white nav" v-if="(type == 'registered') || (type == 'finish')">
					<view class="flex text-center text-black text-bold" style="font-size: 30rpx;">
						<view class="cu-item flex-sub" :class="index==tabCurrent?'text-green cur':''"
							v-for="(item,index) in tabList" :key="index" @tap="tabSelect" :data-id="index">
							{{item}}
						</view>
					</view>
				</scroll-view>
			</view>
			<view class="cu-bar" v-if="(type == 'pending') && (totalCount > 0)">
				<view class="action text-bold">
					待接工单 <text class="cu-tag bg-black radius margin-left-sm">{{totalCount}}</text>
				</view>
			</view>
			<view class="nav-list" :class="type != 'pending' ? 'margin-top-sm' : ''">
				<view class="nav-li bg-white text-black" v-for="(item,index) in repairs" :key="index"
					@click="goInfo(item.id)">
					<view class="nav-title solid-bottom">工单编号：{{item.repair_code || '-'}}</view>
					<view class="nav-name padding-top-sm">设备信息：【{{item.equipment_code}}】{{item.archive_model}}
						{{item.archive_name}}
					</view>
					<view class="nav-name text-cut">故障原因：{{item.content}}</view>
					<view class="nav-name">报修日期：{{item.registertime}}</view>
					<text class="cu-tag radius bg-green">{{item.status_text}}</text>
				</view>
			</view>
		</z-paging>
	</view>
</template>

<script>
	var _self
	var loadFlag = false
	export default {
		data() {
			return {
				repairs: [],
				totalCount: 0,
				type: 'registered',
				tabCurrent: 0,
				tabList: ['待维修', '已完成']
			};
		},
		onShow() {
			if (loadFlag) {
				_self.getRepairs()
			}
		},
		onLoad(e) {
			_self = this

			if ('type' in e) {
				loadFlag = false
				_self.type = e.type
			}
			if (_self.type == 'pending') {
				uni.setNavigationBarTitle({
					title: '待接工单'
				});
			} else {
				uni.setNavigationBarTitle({
					title: '我的工单'
				});
			}
		},
		methods: {
			getRepairs(pageNo, pageSize) {
				_self.$api.repairs({
					type: _self.type,
					page: pageNo,
					pageSize
				}).then((res) => {
					loadFlag = true
					_self.totalCount = res.data.total_count
					_self.$refs.paging.complete(res.data.list)
				}).catch((err) => {
					_self.$refs.paging.complete(false);
				})
			},
			goInfo(id) {
				uni.navigateTo({
					url: '/pages/repair/info?id=' + id
				})
			},
			tabSelect(e) {
				let index = e.currentTarget.dataset.id
				_self.tabCurrent = index
				_self.scrollLeft = (index - 1) * 60
				_self.type = index == 1 ? 'finish' : 'registered'
				_self.$refs.paging.reload()
			}
		}
	}
</script>

<style lang="scss">
	.nav-list {
		display: flex;
		flex-wrap: wrap;
		padding: 0px 20rpx 0px;
		justify-content: space-between;
	}

	.nav-li {
		padding: 30rpx 30rpx 30rpx 38rpx;
		border-radius: 12rpx;
		width: 100%;
		margin: 0 0 20rpx;
		position: relative;
		z-index: 1;
		box-shadow: 0 0 10rpx rgba(0, 0, 0, 0.1);

		.tagLine {
			width: 8rpx;
			height: 100%;
			float: left;
			margin-left: -38rpx;
		}
	}

	.nav-li::after {
		content: "";
		position: absolute;
		z-index: -1;
		background-color: inherit;
		width: 100%;
		height: 100%;
		left: 0;
		bottom: -10%;
		border-radius: 10rpx;
		opacity: 0.2;
		transform: scale(0.9, 0.9);
	}

	.nav-li.cur {
		color: #fff;
		background: rgb(94, 185, 94);
		box-shadow: 4rpx 4rpx 6rpx rgba(94, 185, 94, 0.4);
	}

	.nav-title {
		font-size: 30rpx;
		font-weight: bold;
		padding-bottom: 20rpx;
	}

	.nav-name {
		font-size: 26rpx;
		text-transform: Capitalize;
		margin-top: 10rpx;
		position: relative;
	}

	.nav-li text {
		position: absolute;
		right: 30rpx;
		top: 20rpx;
		font-size: 28rpx;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
	}
	
	.text-green {
		color: #25AC66;
	}
	
	.bg-green {
		background-color: #25AC66;
		color: #FFFFFF;
	}
</style>
