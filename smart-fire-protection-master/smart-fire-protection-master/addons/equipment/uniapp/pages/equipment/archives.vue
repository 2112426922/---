<template>
	<view class="container">
		<z-paging ref="paging" v-model="archives" empty-view-text="暂无设备档案" @query="getArchives">
			<view class="cu-bar" v-if="totalCount > 0">
				<view class="action text-bold">
					档案列表 <text class="cu-tag bg-black radius margin-left-sm">{{totalCount}}</text>
				</view>
			</view>
			<view class="cu-list menu sm-border">
				<view class="cu-item arrow" v-for="(item,index) in archives" :key="index" @click="goEquipemts(item.id)">
					<view class="content">
						<view>【<text class="text-bold">{{item.model}}</text>】{{item.name}}</view>
						<view class="text-gray text-sm">{{item.supplier}}</view>
					</view>
					<view class="action text-grey">{{item.region}}</view>
				</view>
			</view>
		</z-paging>
	</view>
</template>

<script>
	var _self
	export default {
		data() {
			return {
				archives: [],
				totalCount: 0
			};
		},
		onLoad(e) {
			_self = this
		},
		methods: {
			getArchives(pageNo, pageSize) {
				_self.$api.archives({
					page: pageNo,
					pageSize
				}).then((res) => {
					_self.totalCount = res.data.total_count
					_self.$refs.paging.complete(res.data.list)
				}).catch((err) => {
					_self.$refs.paging.complete(false);
				})
			},
			goEquipemts(archiveId) {
				uni.navigateTo({
					url: '/pages/equipment/list?archive_id=' + archiveId
				})
			}
		}
	}
</script>

<style lang="scss">
	.cu-list.menu>.cu-item {
		min-height: 160rpx;
	}
</style>
