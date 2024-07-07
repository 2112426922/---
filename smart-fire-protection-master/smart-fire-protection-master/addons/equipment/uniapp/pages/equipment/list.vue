<template>
	<view class="container">
		<z-paging ref="paging" v-model="equipments" empty-view-text="暂无设备" @query="getLists">
			<view class="cu-bar" v-if="totalCount > 0">
				<view class="action text-bold">
					{{title}} <text class="cu-tag bg-black radius margin-left-sm">{{totalCount}}</text>
				</view>
			</view>
			<view class="nav-list">
				<view class="nav-li bg-white text-black" v-for="(item,index) in equipments" :key="index"
					@click="goInfo(item)">
					<view class="tagLine" :class="'bg-' + item.color"></view>
					<view class="nav-title text-bold">{{item.equipment_code}}</view>
					<view class="nav-name">设备名称：{{item.name}}</view>
					<view class="nav-name">设备型号：{{item.model}}</view>
					<view class="nav-name">所在区域：{{item.region}}</view>
					<text class="cu-tag radius" :class="'bg-' + item.color">{{item.work_status_text}}</text>
				</view>
			</view>
		</z-paging>
	</view>
</template>

<script>
	var _self
	var status
	var keyword
	var searchType
	var planType
	var archiveId
	export default {
		data() {
			return {
				title: '设备列表',
				equipments: [],
				totalCount: 0
			};
		},
		onLoad(e) {
			_self = this
			status = e.status ? e.status : ''
			keyword = e.keyword ? e.keyword : ''
			searchType = e.search_type ? e.search_type : ''
			planType = e.plan_type ? e.plan_type : ''
			archiveId = e.archive_id ? e.archive_id : ''

			switch (planType) {
				case 'inspection':
					_self.title = '设备巡检'
					break;
				case 'maintenance':
					_self.title = '设备保养'
					break;
				default:
					_self.title = '设备列表'
					break;
			}
			if (status == 'scrapped') {
				_self.title = '设备报废'
			}

			uni.$on('manageRefresh', _self.doRefresh)
		},
		onUnload() {
			uni.$off('manageRefresh')
		},
		methods: {
			getLists(pageNo, pageSize) {
				var payLoad = {
					page: pageNo,
					pageSize
				};
				if (status) {
					payLoad.status = status;
				}
				if (planType) {
					payLoad.plan_type = planType;
				}
				if (archiveId) {
					payLoad.archive_id = archiveId;
				}
				if (searchType) {
					payLoad[searchType + '_keyword'] = keyword;
				}

				_self.$api.equipments(payLoad).then((res) => {
					var lists = res.data.list
					lists.forEach(function(item, index) {
						switch (item.work_status) {
							case 'repairing':
								lists[index]['color'] = 'repairing'
								break;
							case 'scrapped':
								lists[index]['color'] = 'scrapped'
								break;
							case 'sickness':
								lists[index]['color'] = 'sickness'
								break;
							case 'normal':
							default:
								lists[index]['color'] = 'normal'
								break;
						}
					})
					
					_self.totalCount = res.data.total_count
					_self.$refs.paging.complete(lists)
				}).catch((err) => {
					_self.$refs.paging.complete(false);
				})
			},
			goInfo(equipment) {
				var type = 'repair'
				if (equipment.work_status == 'repairing') {
					type = 'register'
				}

				uni.navigateTo({
					url: '/pages/equipment/info?type=' + type + '&coding=' + equipment.coding
				})
			},
			doRefresh(e) {
				if (e.ischange) {
					_self.getLists()
				}
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
		font-size: 32rpx;
		font-weight: bold;
		margin-bottom: 10rpx;
	}

	.nav-title::first-letter {
		font-size: 40rpx;
		margin-right: 4rpx;
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
		top: 30rpx;
		font-size: 28rpx;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
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
			margin-top: 228rpx;
		}
	}

	.bg-normal {
		background-color: #25AC66;
		color: #FFFFFF;
	}

	.bg-sickness {
		background-color: #EA9003;
		color: #FFFFFF;
	}

	.bg-scrapped {
		background-color: #999999;
		color: #FFFFFF;
	}

	.bg-repairing {
		background-color: #DD0000;
		color: #FFFFFF;
	}
</style>
